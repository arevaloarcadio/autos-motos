<?php

namespace App\Console\Commands;

use App\Enum\Ad\AdSourceEnum;
use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ColorEnum;
use App\Enum\Ad\ConditionEnum;
use App\Enum\Ad\ImageProcessingStatusEnum;
use App\Enum\Core\ApprovalStatusEnum;
use App\Manager\Market\MarketManager;
use App\Models\Ad;
use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarTransmissionType;
use App\Models\Make;
use App\Models\Models;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\Market;
use App\Models\User;
use App\Output\ImportAdImageOutput;
use App\Output\ImportAdInfoOutput;
use App\Output\ImportSellerInfoOutput;
use App\Output\ImportSellerOutput;
use App\Service\Ad\AdDeleteService;
use App\Service\Ad\Creator\AdCreatorOrchestrator;
use App\Service\Dealer\DealerService;
use App\Service\Dealer\DealerShowRoomService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use SimpleXMLElement;

class ImportMultiPostAdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ads:multipost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Multi Post ads import';
    private MarketManager $marketManager;
    private DealerService $dealerService;
    private DealerShowRoomService $dealerShowRoomService;
    private AdCreatorOrchestrator $adCreator;
    private AdDeleteService $adDeleteService;

    private int $totalAdsCounter = 0;
    private int $successfulAdsCounter = 0;
    private int $updatedAdsCounter = 0;
    private int $skippedAdsCounter = 0;
    private int $adsCount = 0;
    private int $localAdCounter = 0;
    private int $erroredAdsCounter = 0;
    private int $newDealersCreated = 0;
    private array $importedAdsIds = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        MarketManager $marketManager,
        DealerService $dealerService,
        DealerShowRoomService $dealerShowRoomService,
        AdCreatorOrchestrator $adCreator,
        AdDeleteService $adDeleteService
    ) {
        parent::__construct();
        $this->marketManager         = $marketManager;
        $this->dealerService         = $dealerService;
        $this->dealerShowRoomService = $dealerShowRoomService;
        $this->adCreator             = $adCreator;
        $this->adDeleteService       = $adDeleteService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(sprintf('Command started at %s', (new DateTime())->format('Y-m-d H:i:s')));

        $user = $this->getUser();
        auth()->login($user);

        $market = $this->marketManager->findOneBy(['internal_name' => 'spain']);

        $files      = Storage::disk('ftp-s3')->files('multi');
        $totalFiles = count($files);
        $this->info(sprintf('Total files: %d', $totalFiles));

        foreach ($files as $index => $file) {
            $this->info(sprintf('Processing file %s (%d/%d)...', $file, $index + 1, $totalFiles));
            try {
                $fileContent = Storage::disk('ftp-s3')->get($file);
                $xml         = simplexml_load_string($fileContent);
            } catch (Exception $exception) {
                $this->error(
                    sprintf(
                        'Failed to process file %s (%d/%d)... %s',
                        $file,
                        $index + 1,
                        $totalFiles,
                        $exception->getMessage()
                    )
                );
            }


            $dealerOutput = $this->processDealerAndShowRoom($xml->Concesionario, $market);
            if (null === $dealerOutput) {
                continue;
            }

            $this->adsCount = count($xml->Vehiculos->Vehiculo);

            $this->importedAdsIds = [];
            $this->localAdCounter = 0;
            foreach ($xml->Vehiculos->Vehiculo as $ad) {
                $this->totalAdsCounter++;
                $this->localAdCounter++;
                try {
                    $adInfo = $this->generateAdInfo($ad);
                    $this->processAd($adInfo, $user, $market, $dealerOutput);
                } catch (Exception $exception) {
                    $this->error(
                        sprintf(
                            '====> Ad %d errored with message: %s; %d/%d; RAM Used: %s',
                            $ad->NumeroOferta,
                            $exception->getMessage(),
                            $this->localAdCounter,
                            $this->adsCount,
                            $this->getUsedMemory()
                        )
                    );
                    $this->erroredAdsCounter++;
                }
            }

            $this->cleanUpAds($dealerOutput->getDealer());

            Storage::disk('ftp-s3')->delete($file);
            $this->info(sprintf('Successfully imported and deleted file %s', $file));
        }

        $this->info(sprintf('Command ended at %s', (new DateTime())->format('Y-m-d H:i:s')));
        $this->info(sprintf('Total files processed: %d', count($files)));
        $this->info(sprintf('Total new dealers created: %d', $this->newDealersCreated));
        $this->info(sprintf('Total ads processed: %d', $this->totalAdsCounter));
        $this->info(sprintf('Total ads created: %d', $this->successfulAdsCounter));
        $this->info(sprintf('Total ads updated: %d', $this->updatedAdsCounter));
        $this->info(sprintf('Total ads skipped: %d', $this->skippedAdsCounter));
        $this->info(sprintf('Total ads errored: %d', $this->erroredAdsCounter));

        return Command::SUCCESS;
    }

    protected function processDealerAndShowRoom(SimpleXMLElement $container, Market $market): ?ImportSellerOutput
    {
        try {
            $dealerData = new ImportSellerInfoOutput(
                $container->Codigo,
                $container->Nombre,
                $this->formatStringValue($container->CIF),
                $container->Direccion,
                $container->CP,
                (string) $container->Municipio,
                'España',
                $container->Email,
                $this->formatPhoneNumber($container->Telefono)
            );
            $dealer     = $this->findOrCreateDealer($dealerData);
            $showRoom   = $this->findOrCreateDealerShowRoom($dealerData, $dealer, $market);

            return new ImportSellerOutput($dealer, $showRoom);
        } catch (Exception $exception) {
            $this->error(
                sprintf(
                    '==> Failed to load seller with error: %s...; RAM Used: %s',
                    $exception->getMessage(),
                    $this->getUsedMemory()
                )
            );

            return null;
        }
    }

    protected function generateAdInfo(SimpleXMLElement $ad): ImportAdInfoOutput
    {
        $make         = $this->findMake((string) $ad->Marca);
        $model        = $this->findModel((string) $ad->Modelo, $make);
        $transmission = $this->findTransmissionType((string) $ad->CajaCambios);
        $bodyType     = $this->findBodyType((string) $ad->Carroceria);
        $fuelType     = $this->findFuelType((string) $ad->Combustible);
        $images       = $this->processImagesString((string) $ad->Fotos);

        return new ImportAdInfoOutput(
            (string) $ad->NumeroOferta,
            (string) $ad->Nombre,
            (string) $ad->Descripcion,
            $make,
            $model,
            $this->formatFloatValue($ad->PrecioFinal),
            $this->getColor((string) $ad->ColorExterior),
            (int) $this->formatStringValue($ad->Kilometraje),
            $this->getCondition((string) $ad->CategoriaVehiculo),
            $this->processRegistrationDate($ad->PrimeraMatriculacion),
            false,
            $images,
            $this->formatStringValue((string) $ad->Version),
            $transmission,
            $bodyType,
            $fuelType,
            $this->formatIntValue($ad->NumeroPuertas),
            $this->formatIntValue($ad->NumeroAsientos),
            $this->formatIntValue($ad->CilindradaCCM),
            $this->formatIntValue($ad->PotenciaCV),
            $this->formatFloatValue($ad->EmisionesCO2Combinado),
            $this->formatIntValue($ad->NumeroPropietariosAnteriores),
            Carbon::now()
        );
    }


    protected function processAd(
        ImportAdInfoOutput $adInfo,
        User $user,
        Market $market,
        ImportSellerOutput $dealerOutput
    ): Ad {
        $this->importedAdsIds[] = $adInfo->getExternalId();

        $existingAd = $this->findAd($adInfo->getExternalId());
        if ($existingAd instanceof Ad) {
            if (false === $this->updateAd($existingAd, $adInfo)) {
                $this->skippedAdsCounter++;
                $this->info(
                    sprintf(
                        '====> Skipping ad %s; %d/%d; RAM Used: %s',
                        $adInfo->getExternalId(),
                        $this->localAdCounter,
                        $this->adsCount,
                        $this->getUsedMemory()
                    )
                );

                return $existingAd;
            }

            $this->info(
                sprintf(
                    '====> Updated ad %s; %d/%d; RAM Used: %s',
                    $adInfo->getExternalId(),
                    $this->localAdCounter,
                    $this->adsCount,
                    $this->getUsedMemory()
                )
            );

            $this->updatedAdsCounter++;

            return $existingAd;
        }

        $startTime     = microtime(true);
        $ad            = $this->createAd(
            $adInfo,
            $user,
            $market->id,
            $dealerOutput->getDealer(),
            $dealerOutput->getShowRoom()
        );
        $endTime       = microtime(true);
        $executionTime = ($endTime - $startTime);
        $this->info(
            sprintf(
                '====> Ad %d was successfully created in %ss; %d/%d; RAM Used: %s',
                $adInfo->getExternalId(),
                $executionTime,
                $this->localAdCounter,
                $this->adsCount,
                $this->getUsedMemory()
            )
        );
        $this->successfulAdsCounter++;

        return $ad;
    }

    protected function createAd(
        ImportAdInfoOutput $adInfo,
        User $user,
        string $marketId,
        Dealer $dealer,
        DealerShowRoom $showRoom
    ): Ad {
        $adInput = [
            'title'                    => $adInfo->getTitle(),
            'description'              => $adInfo->getDescription(),
            'status'                   => ApprovalStatusEnum::APPROVED,
            'user_id'                  => $user->id,
            'market_id'                => $marketId,
            'source'                   => $this->getSourceName(),
            'external_id'              => $adInfo->getExternalId(),
            'images'                   => [],
            'images_processing_status' => ImageProcessingStatusEnum::PENDING,
            'auto_ad'                  => [
                'price'                        => $adInfo->getPrice(),
                'price_contains_vat'           => $adInfo->isPriceContainsVat(),
                'vin'                          => null,
                'doors'                        => (string) $adInfo->getDoors(),
                'seats'                        => (string) $adInfo->getSeats(),
                'mileage'                      => $adInfo->getMileage(),
                'exterior_color'               => $adInfo->getColor(),
                'interior_color'               => null,
                'condition'                    => $adInfo->getCondition(),
                'dealer_id'                    => $dealer->id,
                'dealer_show_room_id'          => $showRoom->id,
                'email_address'                => $showRoom->email_address,
                'address'                      => $showRoom->address,
                'zip_code'                     => $showRoom->zip_code,
                'city'                         => $showRoom->city,
                'country'                      => $showRoom->country,
                'mobile_number'                => $showRoom->mobile_number,
                'youtube_link'                 => null,
                'ad_fuel_type_id'              => optional($adInfo->getFuelType())->id,
                'ad_body_type_id'              => optional($adInfo->getBodyType())->id,
                'ad_transmission_type_id'      => optional($adInfo->getTransmissionType())->id,
                'ad_drive_type_id'             => null,
                'first_registration_month'     => optional($adInfo->getRegistrationDate())->month,
                'first_registration_year'      => optional($adInfo->getRegistrationDate())->year,
                'engine_displacement'          => $adInfo->getEngineDisplacement(),
                'power_hp'                     => (string) $adInfo->getPowerHp(),
                'owners'                       => (string) $adInfo->getOwners(),
                'inspection_valid_until_month' => null,
                'inspection_valid_until_year'  => null,
                'make_id'                      => $adInfo->getMake()->id,
                'model_id'                     => $adInfo->getModel()->id,
                'generation_id'                => null,
                'series_id'                    => null,
                'trim_id'                      => null,
                'equipment_id'                 => null,
                'additional_vehicle_info'      => $adInfo->getAdditionalVehicleInfo(),
                'co2_emission'                 => $adInfo->getCo2Emissions(),
                'options'                      => [],
            ],
        ];

        if (0 < count($adInfo->getImages())) {
            foreach ($adInfo->getImages() as $image) {
                $adInput['images'][] = [
                    'url'         => $image->getUrl(),
                    'extension'   => $image->getExtension(),
                    'is_external' => true,
                ];
            }
        }

        return $this->adCreator->create(AdTypeEnum::AUTO_SLUG, $adInput);
    }

    protected function updateAd(Ad $ad, ImportAdInfoOutput $adInfo): bool
    {
        if ($adInfo->getLastModified() instanceof Carbon && $ad->autoAd->updated_at >= $adInfo->getLastModified()) {
            return false;
        }

        $changed = false;
        if (null === $ad->autoAd->transmissionType && $adInfo->getTransmissionType() instanceof CarTransmissionType) {
            $ad->autoAd->ad_transmission_type_id = $adInfo->getTransmissionType()->id;
            $changed                             = true;
        }
        if (null === $ad->autoAd->bodyType && $adInfo->getBodyType() instanceof CarBodyType) {
            $ad->autoAd->ad_body_type_id = $adInfo->getBodyType()->id;
            $changed                     = true;
        }
        if (null === $ad->autoAd->fuelType && $adInfo->getFuelType() instanceof CarFuelType) {
            $ad->autoAd->ad_fuel_type_id = $adInfo->getFuelType()->id;
            $changed                     = true;
        }
        if ('other' === $ad->autoAd->exterior_color && 'other' !== $adInfo->getColor()) {
            $ad->autoAd->exterior_color = $adInfo->getColor();
            $changed                    = true;
        }
        if ($ad->autoAd->price !== $adInfo->getPrice()) {
            $ad->autoAd->price = $adInfo->getPrice();

            $changed = true;
        }
        if ($ad->autoAd->price_contains_vat !== $adInfo->isPriceContainsVat()) {
            $ad->autoAd->price_contains_vat = $adInfo->isPriceContainsVat();

            $changed = true;
        }
        if (true === $changed) {
            $ad->autoAd->save();

            return true;
        }

        return false;
    }

    protected function findAd(string $externalId): ?Ad
    {
        /** @var Ad|null $ad */
        $ad = Ad::query()
                ->where('type', '=', AdTypeEnum::AUTO_SLUG)
                ->where('external_id', '=', $externalId)
                ->where('source', '=', $this->getSourceName())
                ->first();

        return $ad;
    }

    /**
     * Deletes the ads that were no longer included for the specified dealer in the import file.
     *
     * @param Dealer $dealer
     */
    protected function cleanUpAds(Dealer $dealer): void
    {
        $ads = Ad::query()
                 ->join('auto_ads', 'auto_ads.ad_id', '=', 'ads.id')
                 ->where('ads.source', '=', $this->getSourceName())
                 ->where('auto_ads.dealer_id', '=', $dealer->id)
                 ->whereNotIn('ads.external_id', $this->importedAdsIds)
                 ->whereNotNull('ads.external_id')
                 ->get();

        $deletedAdsCounter = 0;
        foreach ($ads as $ad) {
            $this->adDeleteService->delete($ad);
            $deletedAdsCounter++;
        }

        $this->info(
            sprintf(
                '==> Deleted %d old ads (%s); RAM Used: %s',
                $deletedAdsCounter,
                $dealer->id,
                $this->getUsedMemory()
            )
        );
    }

    protected function findOrCreateDealer(ImportSellerInfoOutput $dealerData): Dealer
    {
        $dealer = Dealer::query()
                        ->where('vat_number', '=', $dealerData->getVatNumber())
                        ->where('slug', '=', Str::slug($dealerData->getCompanyName()))
                        ->first();

        if ($dealer instanceof Dealer) {
            if (null === $dealer->external_id || null === $dealer->source) {
                $dealer->external_id = $dealerData->getExternalId();
                $dealer->source      = $this->getSourceName();

                $dealer->save();
            }

            return $dealer;
        }

        $dealerInput = [
            'company_name'  => $dealerData->getCompanyName(),
            'vat_number'    => $dealerData->getVatNumber(),
            'address'       => $dealerData->getAddress(),
            'zip_code'      => $dealerData->getZipCode(),
            'city'          => $dealerData->getCity(),
            'country'       => $dealerData->getCountry(),
            'logo_path'     => null,
            'email_address' => $dealerData->getEmailAddress(),
            'phone_number'  => $dealerData->getPhoneNumber(),
            'source'        => $this->getSourceName(),
            'external_id'   => $dealerData->getExternalId(),
        ];

        if ($dealerData->getLogoUrl() !== null && $dealerData->getLogoUrl() !== '') {
            $dealerInput['logo'] = [
                'body' => (string) Image::make($dealerData->getLogoUrl())->encode('data-url'),
            ];
        }

        $dealer = $this->dealerService->create($dealerInput);
        $this->newDealersCreated++;

        return $dealer;
    }

    protected function findOrCreateDealerShowRoom(
        ImportSellerInfoOutput $dealerData,
        Dealer $dealer,
        Market $market
    ): DealerShowRoom {
        if (0 < $dealer->showRooms->count()) {
            return $dealer->showRooms->first();
        }

        $showRoomInput = [
            'name'            => $dealer->company_name,
            'address'         => $dealer->address,
            'zip_code'        => $dealer->zip_code,
            'city'            => $dealer->city,
            'country'         => $dealerData->getCountry(),
            'latitude'        => $dealerData->getLatitude(),
            'longitude'       => $dealerData->getLongitude(),
            'email_address'   => $dealer->email_address,
            'mobile_number'   => $dealer->phone_number,
            'whatsapp_number' => $dealerData->getWhatsappNumber(),
            'market_id'       => $market->id,
            'dealer_id'       => $dealer->id,
        ];

        return $this->dealerShowRoomService->create($showRoomInput);
    }

    protected function getSourceName(): string
    {
        return AdSourceEnum::MULTI_POST_IMPORT;
    }

    /**
     * Get the user to which we will attach all the new ads.
     *
     * @return User
     */
    protected function getUser(): User
    {
        /** @var User $user */
        $user = User::query()->where('email', '=', 'admin@autosmotos.es')->first();

        return $user;
    }

    private function formatStringValue(string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    private function formatFloatValue(string $value): ?float
    {
        if ($value === '' || floatval($value) === 0.0) {
            return null;
        }

        return floatval($value);
    }

    private function formatIntValue(string $value): ?int
    {
        if ($value === '' || intval($value) === 0) {
            return null;
        }

        return intval($value);
    }

    private function formatPhoneNumber(string $phoneNumber): string
    {
        if ($phoneNumber === '') {
            return '-';
        }
        $phoneString  = trim(str_replace([' ', '.'], '', $phoneNumber));
        $phoneNumbers = explode('/', $phoneString);
        $phoneNumber  = array_shift($phoneNumbers);
        if (9 === strlen($phoneNumber)) {
            return sprintf('+34%s', $phoneNumber);
        }

        return $phoneNumber;
    }

    private function findMake(string $externalMake): Make
    {
        if ('' === $externalMake) {
            throw new Exception('no_make');
        }
        $externalMake = mb_strtolower(trim($externalMake));

        $make = Make::query()
                    ->where('ad_type', '=', 'auto')
                    ->where('name', '=', $externalMake)->first();

        $knownMakes = [
            'mercedes'    => 'mercedes-benz',
            'rolls royce' => 'rolls-royce',
            'citroën'     => 'citroen',
            'land-rover'  => 'land rover',
        ];
        if (null === $make && isset($knownMakes[$externalMake])) {
            $make = Make::query()->where('name', '=', $knownMakes[$externalMake])->first();
        }

        if ($make instanceof Make) {
            return $make;
        }

        throw new Exception(sprintf('invalid_make: %s', $externalMake));
    }

    private function findModel(string $externalModel, Make $make)
    {
        if ('' === $externalModel) {
            throw new Exception('no_model');
        }
        $model = $this->queryModel($externalModel, $make->id);

        if (null === $model) {
            $externalModel = mb_strtolower(trim($externalModel), 'UTF-8');
            $model         = $this->queryModel($externalModel, $make->id);
        }

        if (null === $model && Str::contains($externalModel, strtolower($make->name))) {
            $model = $this->queryModel(
                trim(Str::replaceFirst(strtolower($make->name), '', $externalModel)),
                $make->id
            );
        }

        if (null === $model && intval($externalModel) > 0) {
            $model = $this->queryModel((string) intval($externalModel), $make->id);
        }

        if (null === $model && $make->name === 'BMW' && Str::contains($externalModel, 'serie')) {
            $modelParts = explode(' ', $externalModel);
            $model      = $this->queryModel(sprintf('%d Series', $modelParts[1]), $make->id);
        }

        if (null === $model && $make->name === 'Mercedes-Benz' &&
            (Str::contains($externalModel, 'clase') || Str::contains($externalModel, 'classe'))) {
            $modelParts = explode(' ', $externalModel);
            $model      = $this->queryModel(sprintf('%s-Class', $modelParts[1]), $make->id);
        }

        if (null === $model) {
            $modelParts = explode(' ', $externalModel);
            $model      = $this->queryModel($modelParts[0], $make->id);
        }

        if (null === $model) {
            $modelParts = explode(' ', $externalModel);
            if (isset($modelParts[1])) {
                $model = $this->queryModel($modelParts[1], $make->id);
            }
        }

        $knownModels = [
            'clio sporter' => 'Clio',
            'mini'         => 'one',
            'discovery 4'  => 'discovery',
            'evoque'       => 'Range Rover Evoque',
            'cc'           => 'Passat CC',
            'xc-60'        => 'xc60',
        ];
        if (null === $model && isset($knownModels[$externalModel])) {
            $model = $this->queryModel($knownModels[$externalModel], $make->id);
        }

        if ($model instanceof Models) {
            return $model;
        }

        throw new Exception(sprintf('invalid_model for make %s: %s', $make->name, $externalModel));
    }

    private function queryModel(string $name, string $makeId): ?Models
    {
        /** @var Model $instance */
        $instance = Models::query()->where('name', '=', $name)
                         ->where('ad_type', '=', 'auto')
                         ->where('make_id', '=', $makeId)
                         ->first();

        return $instance;
    }

    private function getColor(string $externalColor): string
    {
        $externalColor = strtolower(trim($externalColor));
        $colors        = $this->getColorOptions();

        if (isset($colors[$externalColor])) {
            return $colors[$externalColor];
        }

        return 'other';
    }

    private function getColorOptions(): array
    {
        return [
            'BLANCO GLACIAR METALIZADO' => ColorEnum::WHITE,
            'azul claro'                => ColorEnum::BLUE,
            'verde claro'               => ColorEnum::GREEN,
            'plata'                     => ColorEnum::SILVER,
            'rojo oscuro'               => ColorEnum::RED,
            'gris claro'                => ColorEnum::GRAY,
            'gris-negro'                => ColorEnum::GRAY,
            'rojo'                      => ColorEnum::RED,
            'azul'                      => ColorEnum::BLUE,
            'plateado'                  => ColorEnum::SILVER,
            'blanco'                    => ColorEnum::WHITE,
            'negro'                     => ColorEnum::BLACK,
            'marrón'                    => ColorEnum::BROWN,
            'gris'                      => ColorEnum::GRAY,
            'oro'                       => ColorEnum::GOLD,
            'verde'                     => ColorEnum::GREEN,
            'beige'                     => ColorEnum::BEIGE,
        ];
    }

    private function getCondition(string $externalCondition): string
    {
        $externalCondition = strtolower(trim($externalCondition));
        $conditions        = [
            'vehículos de ocasión' => ConditionEnum::USED,
            'vehículos nuevos'     => ConditionEnum::NEW,
            'KM0'                  => ConditionEnum::NEW,
        ];

        if (isset($conditions[$externalCondition])) {
            return $conditions[$externalCondition];
        }

        return ConditionEnum::OTHER;
    }

    private function findFuelType(string $externalFuel): ?CarFuelType
    {
        if ('' === $externalFuel) {
            return null;
        }
        $externalFuel = strtolower(trim($externalFuel));
        $fuels        = $this->getFuelOptions();

        if (isset($fuels[$externalFuel])) {
            /** @var CarFuelType $fuelType */
            $fuelType = CarFuelType::query()->where('internal_name', '=', $fuels[$externalFuel])
                                   ->where('ad_type', '=', 'auto')
                                   ->first();

            return $fuelType;
        }

        return null;
    }

    private function getFuelOptions(): array
    {
        return [
            'diésel'                    => 'diesel',
            'eléctrico'                 => 'electric',
            'gas'                       => 'gas_gasoline',
            'gas licuado (glp)'         => 'gas_gasoline',
            'gas natural (cng)'         => 'gas_gasoline',
            'gasolina'                  => 'gas_gasoline',
            'híbrido (gasolina)'        => 'hybrid_petrol_electric',
            'híbrido (diésel)'          => 'hybrid_diesel_electric',
            'híbrido'                   => 'other',
            'etanol'                    => 'ethanol',
            'híbrido enchufable (phev)' => 'other',
        ];
    }

    private function findBodyType(string $externalBody): ?CarBodyType
    {
        if ('' === $externalBody) {
            return null;
        }
        $externalBody = strtolower(trim($externalBody));
        $bodyTypes    = $this->getBodyOptions();

        if (isset($bodyTypes[$externalBody])) {
            /** @var CarBodyType $bodyType */
            $bodyType = CarBodyType::query()
                                   ->where('internal_name', '=', $bodyTypes[$externalBody])
                                   ->where('ad_type', '=', 'auto')
                                   ->first();

            return $bodyType;
        }

        return null;
    }

    private function getBodyOptions(): array
    {
        return [
            'berlina'             => 'sedan',
            'cabriolet'           => 'convertible',
            'coche sin carnet'    => null,
            'deportivo'           => 'sport_coupe',
            'familiar'            => 'minivan',
            'furgoneta'           => 'minivan',
            'monovolumen'         => 'minivan',
            'pickup'              => 'suv_crossover',
            'sedan'               => 'sedan',
            'todoterreno'         => 'suv_crossover',
            'utilitario'          => null,
            'vehículo industrial' => null,
            '4x4 SUV'             => 'suv_crossover',
            'coupé'               => 'sport_coupe',
        ];
    }

    private function findTransmissionType(string $externalTransmission): ?CarTransmissionType
    {
        if ('' === $externalTransmission) {
            return null;
        }
        $externalTransmission = strtolower(trim($externalTransmission));
        $transmissions        = $this->getTransmissionOptions();

        if (isset($transmissions[$externalTransmission])) {
            /** @var CarTransmissionType $transmission */
            $transmission = CarTransmissionType::query()
                                               ->where('internal_name', '=', $transmissions[$externalTransmission])
                                               ->where('ad_type', '=', 'auto')
                                               ->first();

            return $transmission;
        }

        return null;
    }

    private function getTransmissionOptions(): array
    {
        return [
            'manual'     => 'manual',
            'automático' => 'automatic',
        ];
    }

    private function processRegistrationDate(string $date): ?Carbon
    {
        if ('' === $date) {
            return null;
        }

        return Carbon::createFromFormat('m.Y', $date);
    }

    /**
     * @param string $imagesString
     *
     * @return ImportAdImageOutput[]
     */
    private function processImagesString(string $imagesString): array
    {
        $images = explode(',', $imagesString);

        return array_map(
            function (string $url) {
                $parts     = explode('.', $url);
                $extension = array_pop($parts);

                return new ImportAdImageOutput($url, $extension);
            },
            $images
        );
    }

    private function getUsedMemory(): string
    {
        return sprintf("%sMB", intval(memory_get_usage(true) / 1024 / 1024));
    }
}
