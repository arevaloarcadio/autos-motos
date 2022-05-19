<?php

namespace App\Console\Commands;

use App\Enum\Ad\AdSourceEnum;
use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ColorEnum;
use App\Enum\Ad\ConditionEnum;
use App\Enum\Ad\ImageProcessingStatusEnum;
use App\Enum\Core\ApprovalStatusEnum;
use App\Exceptions\InvalidAdTypeInputException;
use App\Exceptions\InvalidAdTypeProvidedException;
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
use App\Service\Ad\AdDeleteService;
use App\Service\Ad\Creator\AdCreatorOrchestrator;
use App\Service\Dealer\DealerService;
use App\Service\Dealer\DealerShowRoomService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

class ImportInventarioAdsCommand extends Command
{
    private const COUNTRY_NAME = 'España';
    private const PHONE_PREFIX = '+34';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ads:inventario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invetario.pro ads import';

    /**
     * @var AdCreatorOrchestrator
     */
    private $adCreator;

    /**
     * @var DealerService
     */
    private $dealerService;

    /**
     * @var DealerShowRoomService
     */
    private $dealerShowRoomService;

    /**
     * @var AdDeleteService
     */
    private $adDeleteService;
    /**
     * @var MarketManager
     */
    private $marketManager;

    /**
     * Create a new command instance.
     *
     * @param MarketManager         $marketManager
     * @param DealerService         $dealerService
     * @param DealerShowRoomService $dealerShowRoomService
     * @param AdCreatorOrchestrator $adCreator
     * @param AdDeleteService       $adDeleteService
     */
    public function __construct(
        MarketManager $marketManager,
        DealerService $dealerService,
        DealerShowRoomService $dealerShowRoomService,
        AdCreatorOrchestrator $adCreator,
        AdDeleteService $adDeleteService
    ) {
        parent::__construct();
        $this->adCreator             = $adCreator;
        $this->dealerService         = $dealerService;
        $this->dealerShowRoomService = $dealerShowRoomService;
        $this->adDeleteService       = $adDeleteService;
        $this->marketManager         = $marketManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(sprintf('Command started at %s', (new DateTime())->format('Y-m-d H:i:s')));
        $filePath = $this->saveXmlLocally();
        $xml      = simplexml_load_file($filePath);

        /** @var User $adminUser */
        $adminUser = User::query()->where('email', '=', 'admin@autosmotos.es')->first();
        auth()->login($adminUser);

        $totalAdsCounter      = 0;
        $successfulAdsCounter = 0;
        $updatedAdsCounter    = 0;
        $skippedAdsCounter    = 0;
        $this->output->writeln(
            sprintf(
                'Starting import for %d dealers...',
                count($xml->clientes)
            )
        );
        $importedSellersIds = [];
        /** @var Market $market */
        $market = $this->marketManager->findOneBy(['internal_name' => 'spain']);
        if (null === $market) {
            throw new ModelNotFoundException('The "spain" market does not exist.');
        }
        foreach ($xml->clientes->cliente as $seller) {
            $sellerExternalId = (string) $seller->cliente_id;
            try {
                $sellerAds = $seller->vehiculos;
                $this->info(
                    sprintf('==> Loaded seller ID %s...; RAM Used: %s', $sellerExternalId, $this->getUsedMemory())
                );

                $dealer   = $this->findOrCreateDealer($seller);
                $showRoom = $this->findOrCreateShowRoom($seller, $dealer, $market);
            } catch (Exception $exception) {
                $this->error(
                    sprintf(
                        '==> Failed to load seller %s with error: %s...; RAM Used: %s',
                        $sellerExternalId,
                        $exception->getMessage(),
                        $this->getUsedMemory()
                    )
                );
                continue;
            }

            $importedSellersIds[] = $sellerExternalId;

            $totalAds = count($sellerAds->vehiculo);
            $this->info(
                sprintf('==> Dealer and showroom successfully created; RAM Used: %s', $this->getUsedMemory())
            );
            $this->info(sprintf('==> Importing %d ads; RAM Used: %s', $totalAds, $this->getUsedMemory()));
            $counter        = 0;
            $importedAdsIds = [];
            foreach ($sellerAds->vehiculo as $ad) {
                $totalAdsCounter++;
                $externalId       = (int) $ad->id;
                $importedAdsIds[] = $externalId;

                $existingAd = Ad::query()
                                ->where('type', '=', AdTypeEnum::AUTO_SLUG)
                                ->where('external_id', '=', $externalId)
                                ->where('source', '=', AdSourceEnum::INVENTARIO_IMPORT)
                                ->first();
                if ($existingAd instanceof Ad) {
                    $this->info(
                        sprintf(
                            '====> Ad %d already exists; %d/%d; RAM Used: %s',
                            $externalId,
                            $counter + 1,
                            $totalAds,
                            $this->getUsedMemory()
                        )
                    );
                    $this->updateAd($existingAd, $ad, $updatedAdsCounter, $skippedAdsCounter);
                    $counter++;
                    $successfulAdsCounter++;
                    continue;
                }
                try {
                    $startTime = microtime(true);
                    $this->createAd(
                        $ad,
                        $adminUser,
                        $market->id,
                        $externalId,
                        $dealer,
                        $showRoom
                    );
                    $endTime       = microtime(true);
                    $executionTime = ($endTime - $startTime);
                    $this->info(
                        sprintf(
                            '====> Ad %d was successfully created in %ss; %d/%d; RAM Used: %s',
                            $externalId,
                            $executionTime,
                            $counter + 1,
                            $totalAds,
                            $this->getUsedMemory()
                        )
                    );
                    $successfulAdsCounter++;
                    $counter++;
                } catch (Exception $exception) {
                    $this->error(
                        sprintf(
                            '====> Ad %d errored with message: %s; %d/%d; RAM Used: %s',
                            $externalId,
                            $exception->getMessage(),
                            $counter + 1,
                            $totalAds,
                            $this->getUsedMemory()
                        )
                    );
                    $counter++;
                    continue;
                }
            }

            $this->cleanUpAds($dealer, $importedAdsIds);
            // delete external ads from this dealer not in $importedAdsIds;
        }

        $this->cleanUpDealers($importedSellersIds);
        // find external dealer from current market not in $importedSellersIds and delete their external ads;

        $this->info(
            sprintf(
                '====> Total ads created: %d/%d of which updated: %d; Skipped: %d; RAM Used: %s',
                $successfulAdsCounter,
                $totalAdsCounter,
                $updatedAdsCounter,
                $skippedAdsCounter,
                $this->getUsedMemory()
            )
        );
        $this->info(sprintf('Command ended at %s', (new DateTime())->format('Y-m-d H:i:s')));

        return Command::SUCCESS;
    }

    /**
     * @param Dealer $dealer
     * @param array  $adExternalIds
     */
    private function cleanUpAds(Dealer $dealer, array $adExternalIds): void
    {
        $ads = Ad::query()
                 ->join('auto_ads', 'auto_ads.ad_id', '=', 'ads.id')
                 ->where('auto_ads.dealer_id', '=', $dealer->id)
                 ->where('ads.source', '=', AdSourceEnum::INVENTARIO_IMPORT)
                 ->whereNotIn('ads.external_id', $adExternalIds)
                 ->whereNotNull('ads.external_id')
                 ->get();

        $deletedAdsCounter = 0;
        foreach ($ads as $ad) {
            $this->adDeleteService->delete($ad);
            $deletedAdsCounter++;
        }

        $this->info(
            sprintf(
                '==> Deleted %d old ads from dealer %s; RAM Used: %s',
                $deletedAdsCounter,
                $dealer->id,
                $this->getUsedMemory()
            )
        );
    }

    private function cleanUpDealers(array $externalDealerIds): void
    {
        $dealers = Dealer::query()
                         ->whereNotIn('external_id', $externalDealerIds)
                         ->whereNotNull('external_id')
                         ->where('source', '=', AdSourceEnum::INVENTARIO_IMPORT)
                         ->get();

        $deletedAdsCounter = 0;
        /** @var Dealer $dealer */
        foreach ($dealers as $dealer) {
            $ads = Ad::query()
                     ->select('ads.*')
                     ->join('auto_ads', 'auto_ads.ad_id', '=', 'ads.id')
                     ->where('auto_ads.dealer_id', '=', $dealer->id)
                     ->where('ads.source', '=', AdSourceEnum::INVENTARIO_IMPORT)
                     ->whereNotNull('ads.external_id')
                     ->get();

            foreach ($ads as $ad) {
                $this->adDeleteService->delete($ad);
                $deletedAdsCounter++;
            }
        }

        $this->info(
            sprintf(
                '==> Deleted %d ads from %d dealers no longer in the import; RAM Used: %s',
                $deletedAdsCounter,
                $dealers->count(),
                $this->getUsedMemory()
            )
        );
    }


    private function findOrCreateDealer(SimpleXMLElement $seller): Dealer
    {
        $vatNumber = (string) $seller->cliente_cif === '' ? null : (string) $seller->cliente_cif;
        $dealer    = Dealer::query()
                           ->where('vat_number', '=', $vatNumber)
                           ->where('slug', '=', Str::slug((string) $seller->cliente_nombre))
                           ->first();

        if ($dealer instanceof Dealer) {
            if (null === $dealer->external_id || null === $dealer->source) {
                $dealer->external_id = (string) $seller->cliente_id;
                $dealer->source      = AdSourceEnum::INVENTARIO_IMPORT;

                $dealer->save();
            }

            return $dealer;
        }

        $dealerInput = [
            'company_name'  => (string) $seller->cliente_nombre,
            'vat_number'    => $vatNumber,
            'address'       => (string) $seller->cliente_direccion,
            'zip_code'      => (string) $seller->cliente_codigo_postal,
            'city'          => (string) $seller->cliente_provincia,
            'country'       => self::COUNTRY_NAME,
            'logo_path'     => null,
            'email_address' => (string) $seller->cliente_email,
            'phone_number'  => $this->formatPhoneNumber((string) $seller->cliente_telefono_primero),
            'source'        => AdSourceEnum::INVENTARIO_IMPORT,
            'external_id'   => (string) $seller->cliente_id,
        ];

        return $this->dealerService->create($dealerInput);
    }

    private function findOrCreateShowRoom(
        SimpleXMLElement $sellerInfo,
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
            'country'         => self::COUNTRY_NAME,
            'latitude'        => $this->formatFloatValue((string) $sellerInfo->cliente_lat),
            'longitude'       => $this->formatFloatValue((string) $sellerInfo->cliente_long),
            'email_address'   => $dealer->email_address,
            'mobile_number'   => $dealer->phone_number,
            'whatsapp_number' => $this->formatPhoneNumber((string) $sellerInfo->cliente_whatsapp),
            'market_id'       => $market->id,
            'dealer_id'       => $dealer->id,
        ];

        return $this->dealerShowRoomService->create($showRoomInput);
    }

    private function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (9 === strlen($phoneNumber)) {
            return sprintf('%s%s', self::PHONE_PREFIX, $phoneNumber);
        }

        return $phoneNumber;
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
            'azul claro'  => ColorEnum::BLUE,
            'verde claro' => ColorEnum::GREEN,
            'plata'       => ColorEnum::SILVER,
            'rojo oscuro' => ColorEnum::RED,
            'gris claro'  => ColorEnum::GRAY,
            'gris-negro'  => ColorEnum::GRAY,
            'rojo'        => ColorEnum::RED,
            'azul'        => ColorEnum::BLUE,
            'plateado'    => ColorEnum::SILVER,
            'blanco'      => ColorEnum::WHITE,
            'negro'       => ColorEnum::BLACK,
            'marrón'      => ColorEnum::BROWN,
            'gris'        => ColorEnum::GRAY,
            'oro'         => ColorEnum::GOLD,
            'verde'       => ColorEnum::GREEN,
            'beige'       => ColorEnum::BEIGE,
        ];
    }

    private function getCondition(string $externalCondition): string
    {
        $externalCondition = strtolower(trim($externalCondition));
        $conditions        = [
            'ocasión' => ConditionEnum::USED,
            'nuevo'   => ConditionEnum::NEW,
            'km 0'    => ConditionEnum::NEW,
        ];

        if (isset($conditions[$externalCondition])) {
            return $conditions[$externalCondition];
        }

        return ConditionEnum::OTHER;
    }

    private function findFuelTypeId(string $externalFuel): ?string
    {
        if ('' === $externalFuel) {
            return null;
        }
        $externalFuel = strtolower(trim($externalFuel));
        $fuels        = $this->getFuelOptions();

        if (isset($fuels[$externalFuel])) {
            return CarFuelType::query()->where('internal_name', '=', $fuels[$externalFuel])
                              ->where('ad_type', '=', 'auto')
                              ->first()->id;
        }

        return null;
    }

    private function getFuelOptions(): array
    {
        return [
            'diésel'             => 'diesel',
            'eléctrico'          => 'electric',
            'gas'                => 'gas_gasoline',
            'gas licuado (glp)'  => 'gas_gasoline',
            'gas natural (cng)'  => 'gas_gasoline',
            'gasolina'           => 'gas_gasoline',
            'híbrido (gasolina)' => 'hybrid_petrol_electric',
            'híbrido (diesel)'   => 'hybrid_diesel_electric',
            'etanol'             => 'ethanol',
            'híbrido enchufable' => 'other',
        ];
    }

    private function findBodyTypeId(string $externalBody): ?string
    {
        if ('' === $externalBody) {
            return null;
        }
        $externalBody = strtolower(trim($externalBody));
        $bodyTypes    = $this->getBodyOptions();

        if (isset($bodyTypes[$externalBody])) {
            return CarBodyType::query()
                              ->where('internal_name', '=', $bodyTypes[$externalBody])
                              ->where('ad_type', '=', 'auto')
                              ->first()->id;
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
        ];
    }

    private function findTransmissionTypeId(string $externalTransmission): ?string
    {
        if ('' === $externalTransmission) {
            return null;
        }
        $externalTransmission = strtolower(trim($externalTransmission));
        $transmissions        = $this->getTransmissionOptions();

        if (isset($transmissions[$externalTransmission])) {
            return CarTransmissionType::query()
                                      ->where('internal_name', '=', $transmissions[$externalTransmission])
                                      ->where('ad_type', '=', 'auto')
                                      ->first()->id;
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

    private function processRegistrationDate(string $year, string $month): ?Carbon
    {
        if ('' === $year) {
            throw new Exception('invalid_registration_date');
        }

        if ('' === $month) {
            return Carbon::createFromFormat('m/Y', sprintf('01/%s', $year));
        }

        return Carbon::createFromFormat('m/Y', sprintf('%s/%s', $month, $year));
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

    private function findModel(string $externalModel, Make $make): Models
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

        if ($model instanceof Model) {
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

    private function generateAdditionalVehicleInfo(SimpleXMLElement $ad): string
    {
        return sprintf('%s %s %s', (string) $ad->marca, (string) $ad->modelo, (string) $ad->version);
    }

    private function getUsedMemory(): string
    {
        return sprintf("%sMB", intval(memory_get_usage(true) / 1024 / 1024));
    }

    /**
     * @param Ad               $existingAd
     * @param SimpleXMLElement $ad
     * @param int              $updatedAdsCounter
     */
    private function updateAd(
        Ad $existingAd,
        SimpleXMLElement $ad,
        int &$updatedAdsCounter,
        int &$skippedAdsCounter
    ): bool {
        if ($existingAd->autoAd->updated_at >= Carbon::parse((string) $ad->fch_modificacion)) {
            $skippedAdsCounter++;
            $this->info(
                sprintf(
                    '======> Skipped ad; RAM Used: %s',
                    $this->getUsedMemory()
                )
            );

            return false;
        }
        $changed = false;
        if (null === $existingAd->autoAd->transmissionType) {
            $existingAd->autoAd->ad_transmission_type_id = $this->findTransmissionTypeId((string) $ad->cambio);
            $changed                                     = true;
        }
        if (null === $existingAd->autoAd->bodyType) {
            $existingAd->autoAd->ad_body_type_id = $this->findBodyTypeId((string) $ad->carroceria);
            $changed                             = true;
        }
        if (null === $existingAd->autoAd->fuelType) {
            $existingAd->autoAd->ad_fuel_type_id = $this->findFuelTypeId((string) $ad->combustible);
            $changed                             = true;
        }
        if ('other' === $existingAd->autoAd->exterior_color) {
            $existingAd->autoAd->exterior_color = $this->getColor((string) $ad->color);
            $changed                            = true;
        }
        $price            = (float) $ad->precio;
        $priceContainsVat = (string) $ad->iva_deducible === 'True' ? true : false;
        if ($existingAd->autoAd->price !== $price) {
            $existingAd->autoAd->price = $price;

            $changed = true;
        }
        if ($priceContainsVat !== $existingAd->autoAd->price_contains_vat) {
            $existingAd->autoAd->price_contains_vat = $priceContainsVat;

            $changed = true;
        }
        if (true === $changed) {
            $existingAd->autoAd->save();
            $updatedAdsCounter++;
            $this->info(
                sprintf(
                    '======> Updated ad; RAM Used: %s',
                    $this->getUsedMemory()
                )
            );

            return true;
        }

        return false;
    }

    /**
     * @param SimpleXMLElement $adInfo
     * @param User             $adminUser
     * @param string           $marketId
     * @param int              $externalId
     * @param Dealer           $dealer
     * @param DealerShowRoom   $showRoom
     *
     * @return Ad
     * @throws InvalidAdTypeInputException
     * @throws InvalidAdTypeProvidedException
     * @throws Throwable
     */
    private function createAd(
        SimpleXMLElement $adInfo,
        User $adminUser,
        string $marketId,
        int $externalId,
        Dealer $dealer,
        DealerShowRoom $showRoom
    ): Ad {
        $title            = $this->generateAdditionalVehicleInfo($adInfo);
        $description      = (string) $adInfo->descripcion;
        $registrationDate = $this->processRegistrationDate(
            (string) $adInfo->ano_matriculacion,
            (string) $adInfo->mes_matriculacion
        );
        $make             = $this->findMake((string) $adInfo->marca);
        $model            = $this->findModel((string) $adInfo->modelo, $make);
        $adInput          = [
            'title'                    => $title,
            'description'              => $description,
            'status'                   => ApprovalStatusEnum::APPROVED,
            'user_id'                  => $adminUser->id,
            'market_id'                => $marketId,
            'source'                   => AdSourceEnum::INVENTARIO_IMPORT,
            'external_id'              => $externalId,
            'images'                   => [],
            'images_processing_status' => ImageProcessingStatusEnum::PENDING,
            'auto_ad'                  => [
                'price'                        => (float) $adInfo->precio,
                'price_contains_vat'           => (string) $adInfo->iva_deducible === 'True' ? true : false,
                'vin'                          => null,
                'doors'                        => (string) $this->formatIntValue((string) $adInfo->puertas),
                'seats'                        => (string) $this->formatIntValue((string) $adInfo->asientos),
                'mileage'                      => $this->formatIntValue((string) $adInfo->kilometros),
                'exterior_color'               => $this->getColor((string) $adInfo->color),
                'interior_color'               => null,
                'condition'                    => $this->getCondition((string) $adInfo->tipo),
                'dealer_id'                    => $dealer->id,
                'dealer_show_room_id'          => $showRoom->id,
                'email_address'                => $showRoom->email_address,
                'address'                      => $showRoom->address,
                'zip_code'                     => $showRoom->zip_code,
                'city'                         => $showRoom->city,
                'country'                      => $showRoom->country,
                'mobile_number'                => $showRoom->mobile_number,
                'youtube_link'                 => null,
                'ad_fuel_type_id'              => $this->findFuelTypeId((string) $adInfo->combustible),
                'ad_body_type_id'              => $this->findBodyTypeId((string) $adInfo->carroceria),
                'ad_transmission_type_id'      => $this->findTransmissionTypeId((string) $adInfo->cambio),
                'ad_drive_type_id'             => null,
                'first_registration_month'     => $registrationDate instanceof Carbon ? $registrationDate->month : null,
                'first_registration_year'      => $registrationDate instanceof Carbon ? $registrationDate->year : null,
                'engine_displacement'          => $this->formatIntValue((string) $adInfo->cilindrada),
                'power_hp'                     => (string) $this->formatIntValue((string) $adInfo->potencia),
                'owners'                       => (string) $this->formatIntValue((string) $adInfo->duenos_anteriores),
                'inspection_valid_until_month' => null,
                'inspection_valid_until_year'  => null,
                'make_id'                      => $make->id,
                'model_id'                     => $model->id,
                'generation_id'                => null,
                'series_id'                    => null,
                'trim_id'                      => null,
                'equipment_id'                 => null,
                'additional_vehicle_info'      => $this->generateAdditionalVehicleInfo($adInfo),
                'co2_emission'                 => $this->formatFloatValue((string) $adInfo->emisiones_combinadas),
                'options'                      => [],
            ],
        ];

        $images = $adInfo->imagenes;
        if (count($images) === 0) {
            return $this->adCreator->create(AdTypeEnum::AUTO_SLUG, $adInput);
        }

        foreach ($adInfo->imagenes->imagen as $image) {
            $url                 = (string) $image->imagen_url;
            $parts               = explode('.', $url);
            $extension           = array_pop($parts);
            $adInput['images'][] = [
                'url'         => $url,
                'extension'   => $extension,
                'is_external' => true,
            ];
        }

        return $this->adCreator->create(AdTypeEnum::AUTO_SLUG, $adInput);
    }

    private function saveXmlLocally(): string
    {
        $directory = '/tmp/imports';
        $filePath  = sprintf('%s/%s_inventario.xml', $directory, Carbon::now()->format('dmY'));

        if (false === is_dir($directory)) {
            mkdir($directory);
        }

        if (file_exists($filePath)) {
            return $filePath;
        }

        $rh = fopen(env('INVENTARIO_IMPORT_URL'), 'rb');
        $wh = fopen($filePath, 'wb');

        while ( ! feof($rh)) {
            if (fwrite($wh, fread($rh, 1024)) === false) {
                return '';
            }
        }

        fclose($rh);
        fclose($wh);

        return $filePath;
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
}
