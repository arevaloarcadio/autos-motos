<?php

namespace App\Console\Commands;

use App\Enum\Ad\AdSourceEnum;
use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ImageProcessingStatusEnum;
use App\Enum\Core\ApprovalStatusEnum;
use App\Exceptions\InvalidAdTypeInputException;
use App\Exceptions\InvalidAdTypeProvidedException;
use Illuminate\Support\Facades\Hash;
use App\Models\Ad;
use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarTransmissionType;
use App\Models\Make;
use App\Models\Models;
use App\Models\Dealer;
use App\Models\MotoAd;
use App\Models\TruckAd;
use App\Models\DealerShowRoom;
use App\Models\Market;
use App\Models\User;
use App\Models\AutoAd;
use App\Models\MobileHomeAd;
use App\Models\AdImage;
use App\Service\Ad\AdDeleteService;
use App\Service\Ad\Creator\AdCreatorOrchestrator;
use App\Service\Dealer\DealerService;
use App\Service\Dealer\DealerShowRoomService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use SimpleXMLElement;
use Throwable;
use Traversable;

class ImportPortalClubAdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Portal club ads import';

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
     * Create a new command instance.
     *
     * @param DealerService         $dealerService
     * @param DealerShowRoomService $dealerShowRoomService
     * @param AdCreatorOrchestrator $adCreator
     * @param AdDeleteService       $adDeleteService
     */
    public function __construct(
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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(sprintf('Command started at %s', (new DateTime())->format('Y-m-d H:i:s')));
        $markets       = [
            'it' =>\App\Models\Market::where('internal_name','=','italy')->first()->id,
//                '8cee2a9e-396a-4b19-8631-bfc3e55230b9',
            'es' =>\App\Models\Market::where('internal_name','=','spain')->first()->id
//                '5b8fa498-efe4-4c19-90a8-7285901b4585',
        ];
        $marketCounter = [];
        foreach ($markets as $countryCode => $marketId) {
            $countryName = $countryCode === 'it' ? 'Italia' : 'España';
            $phonePrefix = $countryCode === 'it' ? '+39' : '+34';
            $sellers     = simplexml_load_file(
                sprintf('http://www.portalclub.%s/autosmotos-sellers-feed.xml', $countryCode)
            );
            /** @var User $adminUser */
            $adminUser = User::query()->where('email', '=', 'admin@autosmotos.es')->first();
            auth()->login($adminUser);

            $totalAdsCounter      = 0;
            $successfulAdsCounter = 0;
            $updatedAdsCounter    = 0;
            $skippedAdsCounter    = 0;
            $this->output->writeln(
                sprintf(
                    'Starting import for %d dealers in %s...',
                    count($sellers->seller),
                    $countryName
                )
            );
            $importedSellersIds = [];
            foreach ($sellers->seller as $seller) {
                //                if ((string) $seller->id !== '16327') {
                //                    continue;
                //                }
                $sellerExternalId = (string) $seller->id;
                try {
                    $sellerAds = simplexml_load_file(
                        sprintf(
                            'http://www.portalclub.%s/autosmotos-vehicles-feed/%s.xml',
                            $countryCode,
                            $sellerExternalId
                        )
                    );
                    $this->info(
                        sprintf('==> Loaded seller ID %s...; RAM Used: %s', $seller->id, $this->getUsedMemory())
                    );

                    $sellerInfo = $sellerAds->export->seller;

                    $dealer     = $this->findOrCreateDealer($sellerInfo, $countryName, $phonePrefix);
                    $user     = $this->findUser($sellerInfo,$dealer->id);
                    $showRoom   = $this->findOrCreateShowRoom(
                        $sellerInfo,
                        $dealer,
                        $marketId,
                        $countryName,
                        $phonePrefix
                    );
                } catch (Exception $exception) {
                    $this->error(
                        sprintf(
                            '==> Failed to load seller %s with error: %s... LINE: %s; RAM Used: %s,  ',
                            $seller->id,
                            $exception->getMessage(),
                            $exception->getLine(),
                            $this->getUsedMemory()
                        )
                    );
                    continue;
                }

                $importedSellersIds[] = $sellerExternalId;

                $totalAds = count($sellerAds->export->car);
                $this->info(
                    sprintf('==> Dealer and showroom successfully created; RAM Used: %s', $this->getUsedMemory())
                );
                $this->info(sprintf('==> Importing %d ads; RAM Used: %s', $totalAds, $this->getUsedMemory()));
                $counter        = 0;
                $importedAdsIds = [];
                foreach ($sellerAds->export->car as $ad) {
                    /*if ((string) $ad->genre !== 'auto') {
                        continue;
                    }*/
           
                    $totalAdsCounter++;
                    $externalId       = (int) $ad->attributes()->id;
                    $importedAdsIds[] = $externalId;

                    /*$existingAd = Ad::query()
                                    ->where('type', '=', $ad->genre)
                                    ->where('external_id', '=', $externalId)
                                    ->where('source', '=', AdSourceEnum::PORTAL_CLUB_IMPORT)
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
                        $this->updateAd($existingAd, $ad, $countryCode, $updatedAdsCounter, $skippedAdsCounter, $ad->genre);
                        $counter++;
                        $successfulAdsCounter++;
                        continue;
                    }*/
                    try {
                        $startTime = microtime(true);
                        $this->createAd(
                            $ad,
                            $adminUser,
                            $marketId,
                            $externalId,
                            $countryCode,
                            $dealer,
                            $showRoom,
                            $ad->genre
                        );
                        $endTime       = microtime(true);
                        $executionTime = ($endTime - $startTime);
                       /* $this->info(
                            sprintf(
                                '====> Ad %d was successfully created in %ss; %d/%d; RAM Used: %s',
                                $externalId,
                                $executionTime,
                                $counter + 1,
                                $totalAds,
                                $this->getUsedMemory()
                            )
                        );*/
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

                //$this->cleanUpAds($dealer, $importedAdsIds);
                // delete external ads from this dealer not in $importedAdsIds;
            }

            //$this->cleanUpDealers($countryName, $importedSellersIds);
            // find external dealer from current market not in $importedSellersIds and delete their external ads;

            $marketCounter[$countryCode] = [
                'successful' => $successfulAdsCounter,
                'total'      => $totalAdsCounter,
                'updated'    => $updatedAdsCounter,
                'skipped'    => $skippedAdsCounter,
            ];
        }

        foreach ($marketCounter as $country => $adsNumber) {
            $this->info(
                sprintf(
                    '====> Total ads created for %s: %d/%d of which updated: %d; Skipped: %d; RAM Used: %s',
                    $country,
                    $adsNumber['successful'],
                    $adsNumber['total'],
                    $adsNumber['updated'],
                    $adsNumber['skipped'],
                    $this->getUsedMemory()
                )
            );
        }
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

    private function cleanUpDealers(string $marketCountry, array $externalDealerIds): void
    {
        $dealers = Dealer::query()
                         ->whereNotIn('external_id', $externalDealerIds)
                         ->whereNotNull('external_id')
                         ->where('country', '=', $marketCountry)
                         ->where('source', '=', AdSourceEnum::PORTAL_CLUB_IMPORT)
                         ->get();

        $deletedAdsCounter = 0;
        /** @var Dealer $dealer */
        foreach ($dealers as $dealer) {
            $ads = Ad::query()
                     ->select('ads.*')
                     ->join('auto_ads', 'auto_ads.ad_id', '=', 'ads.id')
                     ->where('auto_ads.dealer_id', '=', $dealer->id)
                     ->where('ads.source', '=', AdSourceEnum::PORTAL_CLUB_IMPORT)
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


    private function generateAdTitle(SimpleXMLElement $ad): string
    {
        if ((string) $ad->title !== '') {
            $title = (string) $ad->title;

            if (strlen($title) >= 5) {
                return $title;
            }
        }

        return $this->generateAdditionalVehicleInfo($ad);
    }


    private function findUser(SimpleXMLElement $externalDealer,$dealer_id): User
    {
        if ('' === $externalDealer) {
            throw new Exception('no_user');
        }

        if ('' === $dealer_id) {
            throw new Exception('no_dealer_id');
        }
        
        $email = strtolower(trim($externalDealer->email));

        $user = User::query()
                    ->where('email', '=', $email)->first();

        if (is_null($user)) {
            
            $user = User::create([
                    'first_name' => $externalDealer->company_name,
                    'last_name' => '.',
                    'email' =>  $email,
                    'password' => Hash::make($externalDealer->email.'123**'),
                    'dealer_id' => $dealer_id,
                    'type' => 'Profesional'
            ]);

            $this->info(sprintf('Successfully registered new user %s',$externalDealer));
        }

        return $user;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateDealer(SimpleXMLElement $sellerInfo, string $countryName, string $phonePrefix): Dealer
    {
        $vatNumber = (string) $sellerInfo->VAT === '' ? null : (string) $sellerInfo->VAT;
        $dealer    = Dealer::query()
                           //->where('vat_number', '=', $vatNumber)
                           ->where('slug', '=', Str::slug($sellerInfo->company_name))
                           ->first();

        $this->info($sellerInfo->logo);
        if ($dealer instanceof Dealer) {
            if (null === $dealer->external_id || null === $dealer->source) {
                $dealer->external_id = (string) $sellerInfo->id;
                $dealer->source      = AdSourceEnum::PORTAL_CLUB_IMPORT;

            }
            
            $dealer->logo_path = $sellerInfo->logo;
			$dealer->save();
            
            return $dealer;
        }


        $dealerInput = [
            'company_name'  => (string) $sellerInfo->company_name,
            'vat_number'    => $vatNumber,
            'slug'          => Str::slug($sellerInfo->company_name),
            'address'       => (string) $sellerInfo->address,
            'zip_code'      => (string) $sellerInfo->zip_code,
            'city'          => (string) $sellerInfo->town,
            'country'       => $countryName,
            'logo_path'     => $sellerInfo->logo,
            'email_address' => (string) $sellerInfo->email,
            'phone_number'  => $this->formatPhoneNumber(
                str_replace(' ', '', (string) $sellerInfo->phone),
                $phonePrefix
            ),
            'source'        => AdSourceEnum::PORTAL_CLUB_IMPORT,
            'external_id'   => (string) $sellerInfo->id,
        ];

        if ((string) $sellerInfo->logo !== '' && (string) $sellerInfo->id !== '543') {
            $dealerInput['logo'] = [
                'body' => (string) Image::make((string) $sellerInfo->logo)->encode('data-url'),
            ];
        }
        return Dealer::create($dealerInput);
       // return $this->dealerService->create($dealerInput);
    }

    private function findOrCreateShowRoom(
        SimpleXMLElement $sellerInfo,
        Dealer $dealer,
        string $marketId,
        string $countryName,
        string $phonePrefix
    ): DealerShowRoom {
        if (0 < $dealer->showRooms->count()) {
            return $dealer->showRooms->first();
        }

        $showRoomInput = [
            'name'          => (string) $sellerInfo->company_name,
            'address'       => (string) $sellerInfo->address,
            'zip_code'      => (string) $sellerInfo->zip_code,
            'city'          => (string) $sellerInfo->town,
            'country'       => $countryName,
            'latitude'      => (string) $sellerInfo->latitude === '' ? null : (string) $sellerInfo->latitude,
            'longitude'     => (string) $sellerInfo->longitude === '' ? null : (string) $sellerInfo->longitude,
            'email_address' => (string) $sellerInfo->email,
            'mobile_number' => $this->formatPhoneNumber(
                str_replace(' ', '', (string) $sellerInfo->phone),
                $phonePrefix
            ),
            'market_id'     => $marketId,
        ];

        $showRoomInput['dealer_id'] = $dealer->id;

        return $this->dealerShowRoomService->create($showRoomInput);
    }

    private function formatPhoneNumber(string $phoneNumber, string $phonePrefix): string
    {
        if (9 === strlen($phoneNumber)) {
            return sprintf('%s%s', $phonePrefix, $phoneNumber);
        }

        return $phoneNumber;
    }

    private function getColor(string $externalColor, string $countryCode): string
    {
        $externalColor = strtolower(trim($externalColor));
        $colors        = $this->getColorOptions($countryCode);

        if (isset($colors[$externalColor])) {
            return $colors[$externalColor];
        }

        return 'other';
    }

    private function getColorOptions(string $countryCode): array
    {
        if ($countryCode === 'es') {
            return [
                'rojo'     => 'red',
                'azul'     => 'blue',
                'plateado' => 'silver',
                'blanco'   => 'white',
                'negro'    => 'black',
                'marrón'   => 'brown',
                'gris'     => 'gray',
                'oro'      => 'gold',
                'verde'    => 'green',
                'beige'    => 'beige',
            ];
        }

        return [
            'rossa'         => 'red',
            'blu'           => 'blue',
            'argento'       => 'silver',
            'bianco'        => 'white',
            'nero'          => 'black',
            'marrone'       => 'brown',
            'grigio'        => 'gray',
            'grigio chiaro' => 'gray',
            'grigio scuro'  => 'gray',
            'oro'           => 'gold',
            'verde'         => 'green',
            'verde scuro'   => 'green',
            'verde chiaro'  => 'green',
            'beige'         => 'beige',
        ];
    }

    private function getCondition(string $externalCondition): string
    {
        $externalCondition = strtolower(trim($externalCondition));
        $conditions        = [
            'kmzero'  => 'new',
            'nuovo'   => 'new',
            'ocasion' => 'used',
            'usato'   => 'used',
        ];

        if (isset($conditions[$externalCondition])) {
            return $conditions[$externalCondition];
        }

        return 'other';
    }

    private function generateYouTubeLink(string $code): ?string
    {
        if ($code === '') {
            return null;
        }

        return sprintf('https://www.youtube.com/embed/%s', $code);
    }

    private function findFuelTypeId(string $externalFuel, string $countryCode): ?string
    {
        if ('' === $externalFuel) {
            return 'ed20075a-5297-11eb-b5ca-02e7c1e23b94';
        }
        $externalFuel = strtolower(trim($externalFuel));
        $fuels        = $this->getFuelOptions($countryCode);

        if (isset($fuels[$externalFuel])) {
            $car_fuel_type = CarFuelType::query()->where('internal_name', '=', $fuels[$externalFuel])
                              //->where('ad_type', '=', 'auto')
                              ->first();
                              
            if (!is_null($car_fuel_type)) {
               return $car_fuel_type['id'];
            }
             
        }

        return 'ed20075a-5297-11eb-b5ca-02e7c1e23b94';
    }

    private function getFuelOptions(string $countryCode): array
    {
        if ('es' === $countryCode) {
            return [
                'gasolina'          => 'petrol',
                'diesel'            => 'diesel',
                'gas licuado (glp)' => 'gas_gasoline',
                'gas natural (cng)' => 'gas_gasoline',
                'etanol'            => 'ethanol',
                'electrico'         => 'electric',
                'hidrogeno'         => 'other',
                'electro/gasolina'  => 'hybrid_petrol_electric',
                'electro/diesel'    => 'hybrid_diesel_electric',
                'otros'             => 'other',
            ];
        }

        return [
            'benzina'           => 'petrol',
            'diesel'            => 'diesel',
            'gpl'               => 'gas_gasoline',
            'solo gpl'          => 'gas_gasoline',
            'elettrica'         => 'electric',
            'elettrica-benzina' => 'hybrid_petrol_electric',
            'elettrica-diesel'  => 'hybrid_diesel_electric',
            'altro'             => 'other',
            'metano'            => 'other',
        ];
    }

    private function findBodyTypeId(string $externalBody, string $countryCode): ?string
    {
        if ('' === $externalBody) {
            return '1492cecf-2568-4704-8d46-297f4d41fb9c';
        }
        $externalBody = strtolower(trim($externalBody));
        $bodyTypes    = $this->getBodyOptions($countryCode);

        if (isset($bodyTypes[$externalBody])) {
            $body = CarBodyType::query()
                              ->where('internal_name', '=', $bodyTypes[$externalBody])
                              //->where('ad_type', '=', 'auto')
                              ->first();

            if (!is_null($body)) {
                return $body['id'];
            }
        }

        return '1492cecf-2568-4704-8d46-297f4d41fb9c';
    }

    private function getBodyOptions(string $countryCode): array
    {
        if ('es' === $countryCode) {
            return [
                'sedan'           => 'sedan',
                'bus'             => null,
                'cabrio'          => 'convertible',
                'coupè'           => 'sport_coupe',
                '4 X 4'           => 'suv_crossover',
                'transporter'     => 'suv_crossover',
                'furgoneta'       => 'minivan',
                'otro'            => null,
                'station wagon'   => 'wagon',
                'monovolumen'     => 'minivan',
                'suv'             => 'suv_crossover',
                'pick up'         => 'suv_crossover',
                'sin licencia'    => null,
                'dos volúmenes'   => 'hatchback',
                'coche pequeño'   => 'hatchback',
                'city car'        => 'hatchback',
                'utility car'     => null,
                'sport'           => 'sport_coupe',
                '4x4 todoterreno' => 'suv_crossover',
                'pick up 4x4'     => 'suv_crossover',
                'turismo'         => null,
                'berlina'         => 'sedan',
                'tres volúmenes'  => 'wagon',
            ];
        }

        return [
            'sedan'           => 'sedan',
            'bus'             => null,
            'cabrio'          => 'convertible',
            'coupè'           => 'sport_coupe',
            '4 X 4'           => 'suv_crossover',
            'transporter'     => 'suv_crossover',
            'furgoneta'       => 'minivan',
            'otro'            => null,
            'station wagon'   => 'wagon',
            'monovolume'      => 'minivan',
            'suv'             => 'suv_crossover',
            'pick up'         => 'suv_crossover',
            'fuoristrada'     => 'suv_crossover',
            'sin licencia'    => null,
            'due volumi'      => 'hatchback',
            'coche pequeño'   => 'hatchback',
            'city car'        => 'hatchback',
            'utility car'     => null,
            'sport'           => 'sport_coupe',
            '4x4 todoterreno' => 'suv_crossover',
            'pick up 4x4'     => 'suv_crossover',
            'turismo'         => null,
            'berlina'         => 'sedan',
            'tre volumi'      => 'wagon',
        ];
    }

    private function findTransmissionTypeId(string $externalTransmission, string $countryCode): ?string
    {
        if ('' === $externalTransmission) {
            return null;
        }
        $externalTransmission = strtolower(trim($externalTransmission));
        $transmissions        = $this->getTransmissionOptions($countryCode);

        if (isset($transmissions[$externalTransmission])) {
            $transmissions =  CarTransmissionType::query()
                                      ->where('internal_name', '=', $transmissions[$externalTransmission])
                                      //->where('ad_type', '=', 'auto')
                                      ->first();
            if (!is_null($transmissions)) {
                $transmissions['id'];
            }
        }

        return null;
    }

    private function getTransmissionOptions(string $countryCode): array
    {
        if ('es' === $countryCode) {
            return [
                'manual'     => 'manual',
                'automatico' => 'automatic',
            ];
        }

        return [
            'manuale'    => 'manual',
            'automatico' => 'automatic',
        ];
    }

    

    private function processRegistrationDate(string $registrationDate): ?Carbon
    {
        if ('' === $registrationDate) {
            throw new Exception('invalid_registration_date');
        }

        if (strlen($registrationDate) === 4) {
            return Carbon::createFromFormat('m/Y', sprintf('01/%s', $registrationDate));
        }

        return Carbon::createFromFormat('m/Y', $registrationDate);
    }

    private function findMake(string $externalMake): Make
    {
        if ('' === $externalMake) {
            throw new Exception('no_make');
        }
        $externalMake = strtolower(trim($externalMake));

        $make = Make::query()
                    //->where('ad_type', '=', 'auto')
                    ->where('name', '=', $externalMake)->first();

        $knownMakes = [
            'mercedes'    => 'mercedes-benz',
            'rolls royce' => 'rolls-royce',
        ];
        if (null === $make && isset($knownMakes[$externalMake])) {
            $make = Make::query()->where('name', '=', $knownMakes[$externalMake])->first();
        }

        if ($make instanceof Make) {
            return $make;
        }

        throw new Exception(sprintf('invalid_make: %s', $externalMake));
    }

    private function findModel(string $externalModel, Make $make,$gener): Models
    {
        if ('' === $externalModel) {
            throw new Exception('no_model');
        }
        $model = $this->queryModel($externalModel, $make->id);

        if (null === $model) {
            //$externalModel = mb_strtolower(trim($externalModel), 'UTF-8');
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
        ];
        if (null === $model && isset($knownModels[$externalModel])) {
            $model = $this->queryModel($knownModels[$externalModel], $make->id);
        }

        if ($model instanceof Models) {
            return $model;
        }else{

            $this->info(sprintf('Save new External Model: %s , Mark: %s', $externalModel ,$make->name));

            $slug = Models::where('slug',Str::slug($externalModel))->count();
            
            $model = new Models;
            $model->name = $externalModel;
            $model->slug =  $slug == 0 ? Str::slug($externalModel) : Str::slug($externalModel).'-'.random_int(1000, 9999);
            $model->make_id = $make->id;
            $model->ad_type = $gener;
            $model->external_updated_at = Carbon::now();
            $model->save();

            return $model;
        }



        //throw new Exception(sprintf('invalid_model for make %s: %s', $make->name, $externalModel));
    }

    private function queryModel(string $name, string $makeId)
    {
        /** @var Model $instance */
        $instance = Models::query()->where('name', '=', $name)
                         //->where('ad_type', '=', 'auto')
                         ->where('make_id', '=', $makeId)
                         ->first();

        return $instance;
    }

    private function generateAdditionalVehicleInfo(SimpleXMLElement $ad): string
    {
        return sprintf('%s %s %s', (string) $ad->model->make, (string) $ad->model->model, (string) $ad->model->version);
    }

    private function getUsedMemory(): string
    {
        return sprintf("%sMB", intval(memory_get_usage(true) / 1024 / 1024));
    }

    /**
     * @param Ad               $existingAd
     * @param SimpleXMLElement $ad
     * @param string           $countryCode
     * @param int              $updatedAdsCounter
     */
    private function updateAd(
        Ad $existingAd,
        SimpleXMLElement $ad,
        string $countryCode,
        int &$updatedAdsCounter,
        int &$skippedAdsCounter,
        $gener
    ): void {

        $key = '';

        if ($gener == 'auto') {
           $key = 'autoAd';
        }

        if ($gener == 'moto') {
           $key = 'motoAd';
        }

        if ($gener == 'furgone') {
           $key = 'mobileHomeAd';
        }

        $this->info($gener.'->'.$key);
        
        if (is_null($existingAd[$key])) {
            return;
        }
        
        if ($existingAd[$key]->updated_at >= Carbon::parse((string) $ad->last_modified)) {
            $skippedAdsCounter++;
            $this->info(
                sprintf(
                    '======> Skipped ad; RAM Used: %s',
                    $this->getUsedMemory()
                )
            );

            return;
        }
        $changed = false;
        if (null === $existingAd[$key]->transmissionType) {
            $existingAd[$key]->ad_transmission_type_id = $this->findTransmissionTypeId(
                (string) $ad->gearbox,
                $countryCode
            );
            $changed                                     = true;
        }
        if (null === $existingAd[$key]->bodyType) {
            $existingAd[$key]->ad_body_type_id = $this->findBodyTypeId(
                (string) $ad->model->body,
                $countryCode
            );
            $changed                             = true;
        }
        if (null === $existingAd[$key]->fuelType) {
            $existingAd[$key]->ad_fuel_type_id = $this->findFuelTypeId(
                (string) $ad->model->fuel,
                $countryCode
            );
            $changed                             = true;
        }
        if ('other' === $existingAd[$key]->exterior_color) {
            $existingAd[$key]->exterior_color = $this->getColor(
                (string) $ad->exterior->color,
                $countryCode
            );
            $changed                            = true;
        }
        if ('other' === $existingAd[$key]->interior_color) {
            $existingAd[$key]->interior_color = $this->getColor(
                (string) $ad->interior->color,
                $countryCode
            );
            $changed                            = true;
        }
        $price            = (float) $ad->customers_price;
        $priceContainsVat = (string) $ad->claimable_vat === '' ? false : (string) $ad->claimable_vat === 'true';
        if ($existingAd[$key]->price !== $price) {
            $existingAd[$key]->price = $price;

            $changed = true;
        }
        if ($priceContainsVat !== $existingAd[$key]->price_contains_vat) {
            $existingAd[$key]->price_contains_vat = $priceContainsVat;

            $changed = true;
        }
        if (true === $changed) {
            $existingAd[$key]->save();
            $updatedAdsCounter++;
            $this->info(
                sprintf(
                    '======> Updated ad; RAM Used: %s',
                    $this->getUsedMemory()
                )
            );
        }
    }

    /**
     * @param SimpleXMLElement $adInfo
     * @param User             $adminUser
     * @param string           $marketId
     * @param int              $externalId
     * @param string           $countryCode
     * @param Dealer           $dealer
     * @param DealerShowRoom   $showRoom
     *
     * @return Ad
     * @throws InvalidAdTypeInputException
     * @throws InvalidAdTypeProvidedException
     * @throws Throwable
     */

    private function findOrCreateAd($external_ad,$dealer_id): Ad
    {
        if (count($external_ad) == 0) {
            throw new Exception('no_external_ad');
        }        

        $ad = Ad::where('title',$external_ad['title'])->first();
        
        if (is_null($ad)) {

            $ad = Ad::create($external_ad);

            $this->info(sprintf('Successfully registered new %s, %s',$external_ad['type'],$external_ad['external_id']));
        }else{

            $external_ad['slug'] .= random_int(1000, 9999);
            
            $ad = Ad::create($external_ad);

            $this->info(sprintf('2: Successfully registered new %s, %s',$external_ad['type'],$external_ad['external_id']));
        }
        
        return $ad;
    }


    private function validateAd($external_ad,$external_vehicle_ad)
    {
        $ad = Ad::where('title',$external_ad['title'])
            ->where('description',$external_ad['description'])
            ->where('type',$external_ad['type'])
            ->first();
        
        if (is_null($ad)) {
            return false;
        }else{

            switch ($ad['type']) {
                case 'auto':
                    
                    $auto_ad = AutoAd::where('dealer_id',$external_vehicle_ad['dealer_id'])
                        ->where('address',$external_vehicle_ad['address'])
                        ->first();
                    
                    if (is_null($auto_ad)) {
                        return false;
                    }else{
                        return true;
                    }
                    
                    break;
                case 'moto':

                   $moto_ad = MotoAd::where('dealer_id',$external_vehicle_ad['dealer_id'])
                        ->where('address',$external_vehicle_ad['address'])
                        ->first();
                    
                    if (is_null($moto_ad)) {
                        return false;
                    }else{
                        return true;
                    }

                    break;
                case 'mobile-home':
                    $mobile_ad = MobileHomeAd::where('dealer_id',$external_vehicle_ad['dealer_id'])
                        ->where('address',$external_vehicle_ad['address'])
                        ->first();

                    if (is_null($mobile_ad)) {
                        return false;
                    }else{
                        return true;
                    }
                    break;
                case 'truck':

                    $truck_ad = TruckAd::where('dealer_id',$external_vehicle_ad['dealer_id'])
                        ->where('address',$external_vehicle_ad['address'])
                        ->first();

                    if (is_null($truck_ad)) {
                        return false;
                    }else{
                        return true;
                    }

                    break;    
                default:
                    # code...
                    break;
            }
            
        }
        
        return $ad;
    }
    private function findOrCreateAutoAd($external_auto_ad,$ad): AutoAd
    {
        if (count($external_auto_ad) == 0) {
            throw new Exception('external_auto_ad');
        }

        $auto_ad = AutoAd::query()
                    ->where('ad_id', '=', $ad['id'])->first();

        if (is_null($auto_ad)) {
            
            $auto_ad = AutoAd::create($external_auto_ad);
            $this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
        }else{
            $auto_ad->update($external_auto_ad); 
            $this->info(sprintf('Successfully modify auto_ad %s',$ad['external_id']));
        }

        return $auto_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateMotoAd($external_auto_ad,$ad): MotoAd
    {
        if (count($external_auto_ad) == 0) {
            throw new Exception('external_auto_ad');
        }

        $moto_ad = MotoAd::query()
                    ->where('ad_id', '=', $ad['id'])->first();

        if (is_null($moto_ad)) {
            
            $moto_ad = MotoAd::create($external_auto_ad);

            $this->info(sprintf('Successfully registered new moto_ad %s',$ad['external_id']));
        }else{
            $moto_ad->update($external_auto_ad); 
            $this->info(sprintf('Successfully modify moto_ad %s',$ad['external_id']));
        }

        return $moto_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateTruckAd($external_auto_ad,$ad): TruckAd
    {
        if (count($external_auto_ad) == 0) {
            throw new Exception('external_auto_ad');
        }

        $truck_ad = TruckAd::query()
                    ->where('ad_id', '=', $ad['id'])->first();

        if (is_null($truck_ad)) {
            
            $truck_ad = TruckAd::create($external_auto_ad);

            $this->info(sprintf('Successfully registered new truck_ad %s',$ad['external_id']));
        }else{
            $truck_ad->update($external_auto_ad); 
            $this->info(sprintf('Successfully modify truck_ad %s',$ad['external_id']));
        }

        return $truck_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateMobileHomeAd($external_auto_ad,$ad): MobileHomeAd
    {
        if (count($external_auto_ad) == 0) {
            throw new Exception('external_auto_ad');
        }

        $mobile_home_ad = MobileHomeAd::query()
                    ->where('ad_id', '=', $ad['id'])->first();

        if (is_null($mobile_home_ad)) {
            
            $mobile_home_ad = MobileHomeAd::create($external_auto_ad);

            $this->info(sprintf('Successfully registered new mobile_home_ad %s',$ad['external_id']));
        }else{
            $mobile_home_ad->update($external_auto_ad); 
            $this->info(sprintf('Successfully modify truck_ad %s',$ad['external_id']));
        }

        return $mobile_home_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }
    private function createAd(
        SimpleXMLElement $adInfo,
        User $user,
        string $marketId,
        int $externalId,
        string $countryCode,
        Dealer $dealer,
        DealerShowRoom $showRoom,
        $gener
    ) {

        $type = $this->getTypeAd($gener);

        $this->info(  $adInfo->model->body);
       
        $title            = $this->generateAdTitle($adInfo);
        $description      = (string) $adInfo->additional_informations;
        $registrationDate = $this->processRegistrationDate((string) $adInfo->registration_date);
        $make             = $this->findMake((string) $adInfo->model->make);
        $model            = $this->findModel((string) $adInfo->model->model, $make,strtoupper($type));
        
        $adInput = [
            'title'                    => $title,
            'description'              => $description,
            'slug'                     => Str::slug($title),
            'status'                   => ApprovalStatusEnum::APPROVED,
            'user_id'                  => $user->id,
            'market_id'                => $marketId,
            'source'                   => AdSourceEnum::PORTAL_CLUB_IMPORT,
            'external_id'              => $externalId,
            'images'                   => [],
            'images_processing_status' => ImageProcessingStatusEnum::PENDING,
            'type' =>  $type,
        ];


        $vehicleAd = [
                'price'                        => (float) $adInfo->customers_price,
                'price_contains_vat'           => (string) $adInfo->claimable_vat === '' ? false : (string) $adInfo->claimable_vat === 'true',
                'vin'                          => (string) $adInfo->vin === '' ? null : (string) $adInfo->vin,
                'doors'                        => (string) $adInfo->model->doors === '' ? null : (string) $adInfo->model->doors,
                'seats'                        => (string) $adInfo->model->seats === '' ? null : (string) $adInfo->model->seats,
                'mileage'                      => (string) $adInfo->km === '' ? 0 : (int) $adInfo->km,
                'exterior_color'               => $this->getColor(
                    (string) $adInfo->exterior->color,
                    $countryCode
                ),
                'color'               => $this->getColor(
                    (string) $adInfo->exterior->color,
                    $countryCode
                ),
                'interior_color'               => $this->getColor(
                    (string) $adInfo->interior->color,
                    $countryCode
                ),
                'condition'                    => $this->getCondition(
                    (string) $adInfo->usage
                ),
                'dealer_id'                    => $dealer->id,
                'dealer_show_room_id'          => $showRoom->id,
                'email_address'                => $showRoom->email_address,
                'address'                      => $showRoom->address,
                'zip_code'                     => $showRoom->zip_code,
                'city'                         => $showRoom->city,
                'country'                      => $showRoom->country,
                'mobile_number'                => $showRoom->mobile_number,
                'youtube_link'                 => $this->generateYouTubeLink(
                    (string) $adInfo->youtube_code
                ),
                'ad_fuel_type_id'              => $this->findFuelTypeId(
                    (string) $adInfo->model->fuel,
                    $countryCode
                ),
                'ad_body_type_id'              => $this->findBodyTypeId(
                    (string) $adInfo->model->body,
                    $countryCode
                ),
                'ad_transmission_type_id'      => $this->findTransmissionTypeId(
                    (string) $adInfo->gearbox,
                    $countryCode
                ),
                'ad_drive_type_id'             => null,

                'fuel_type_id'                 => $this->findFuelTypeId(
                    (string) $adInfo->model->fuel,
                    $countryCode
                ),
                'body_type_id'                 => $this->findBodyTypeId(
                    (string) $adInfo->model->body,
                    $countryCode
                ),
                'transmission_type_id'         => $this->findTransmissionTypeId(
                    (string) $adInfo->gearbox,
                    $countryCode
                ),
                'vehicle_category_id'          => 'b0578de4-8c44-4ef9-ae74-cd736062f93a',
                'drive_type_id'                => null,
                'first_registration_month'     => $registrationDate instanceof Carbon ? $registrationDate->month : null,
                'first_registration_year'      => $registrationDate instanceof Carbon ? $registrationDate->year : null,
                'engine_displacement'          => (string) $adInfo->model->cc === '' ? null : (int) $adInfo->model->cc,
                'power_hp'                     => (string) $adInfo->model->hp === '' ? null : (string) $adInfo->model->hp,
                'owners'                       => (string) $adInfo->previous_owners === '' ? null : (string) $adInfo->previous_owners,
                'model' => $adInfo->model->model,
                'truck_type' => 'Car transporter',
                'inspection_valid_until_month' => null,
                'inspection_valid_until_year'  => null,
                'make_id'                      => $make->id,
                'model_id'                     => $model->id,
                'generation_id'                => null,
                'series_id'                    => null,
                'trim_id'                      => null,
                'equipment_id'                 => null,
                'additional_vehicle_info'      => $this->generateAdditionalVehicleInfo($adInfo),
                'co2_emission'                 => (string) $adInfo->model->CO2_emission === '' ? null : (float) $adInfo->model->CO2_emission,
                'cylinders'                    => (string) $adInfo->model->cylinders === '' ? null : (float) $adInfo->model->cylinders,
                'options'                      => [],

            ];

        
        try{

            \Illuminate\Support\Facades\Log::build(['driver' => 'single', 'path' => storage_path('logs/portal_club_'.date('dmy').'.log')])->debug($gener.' - body '.$adInfo->model->body.' - fuel'.$adInfo->model->fuel);

            if ($type == 'moto') {
                
                if (!$this->validateAd($adInput,$vehicleAd)) {

                    $ad = $this->findOrCreateAd($adInput,$dealer->id);
                    $vehicleAd['ad_id'] = $ad->id;
                    
                    $this->storeAdImage($ad,$adInfo->images->image);
                    
                    return $this->findOrCreateMotoAd($vehicleAd,$ad);
                }else{
                     $this->info(sprintf('Ad Skipped %s, %s, %s',$adInput['external_id'],$gener,$type));
                    return null;
                }
                
            }
            
            if ($type == 'auto') {
                if (!$this->validateAd($adInput,$vehicleAd)) {
                    $ad = $this->findOrCreateAd($adInput,$dealer->id);
                    
                    $vehicleAd['ad_id'] = $ad->id;
                    $this->storeAdImage($ad,$adInfo->images->image);
               
                    return $this->findOrCreateAutoAd($vehicleAd,$ad);
                }else{
                     $this->info(sprintf('Ad Skipped %s, %s, %s',$adInput['external_id'],$gener,$type));
                    return null;
                }
            }

            if ($type == 'mobile-home') {

                if (!$this->validateAd($adInput,$vehicleAd)) {
                    $ad = $this->findOrCreateAd($adInput,$dealer->id);
                    
                    $vehicleAd['ad_id'] = $ad->id;
                    $vehicleAd['vehicle_category_id'] = $this->getCategory($gener);
                    
                    $this->storeAdImage($ad,$adInfo->images->image);
                    
                    return $this->findOrCreateMobileHomeAd($vehicleAd,$ad);
                }else{
                     $this->info(sprintf('Ad Skipped %s, %s, %s',$adInput['external_id'],$gener,$type));
                    return null;
                }
            }

            if ($type == 'truck') {

                if (!$this->validateAd($adInput,$vehicleAd)) {
                    $ad = $this->findOrCreateAd($adInput,$dealer->id);
                    
                    $vehicleAd['ad_id'] = $ad->id;
                    $vehicleAd['vehicle_category_id'] = $this->getCategory($gener);
                    
                    $this->storeAdImage($ad,$adInfo->images->image);
                    
                    return $this->findOrCreateTruckAd($vehicleAd,$ad);
                }else{
                     $this->info(sprintf('Ad Skipped %s, %s, %s',$adInput['external_id'],$gener,$type));
                    return null;
                }
            }

            

        }catch (Exception $e) {

            $this->info(
                sprintf(
                    '==>Error: %s , %s, %s ,%s',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile(),
                    $this->getUsedMemory()
                )
            );
            
            \Illuminate\Support\Facades\Log::build(['driver' => 'single', 'path' => storage_path('logs/portal_club_'.date('dmy').'.log')])->debug(sprintf(
                    '==>Error: %s , %s, %s ,%s',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile(),
                    $this->getUsedMemory()
                ));
        }
    }
    
     public function getTypeAd($body)
    {   
        $bodys_inventario = [
            'auto' => 'auto',
            'moto' => 'moto',
            'autocarro' => 'truck',
            'furgone'    => 'truck',
            'autoarticolato' => 'truck',
            'semirimorchio' => 'truck',
            'veicolo comunale' => 'truck',
            'macchina agricola' => 'truck',
            'macchina edile' => 'truck',
            'bus' => 'truck',
            'carrello elevatore' => 'truck',
            'roulotte' => 'mobile-home',
            'casa mobile' => 'mobile-home',
        ];

        return $bodys_inventario[trim($body)];
    }

    public function getCategory($body)
    {   
        $bodys_inventario = [
            'autocarro' => '9a3ca7bc-3f5f-11ed-b552-960000d5cd75',
            'furgone'    => '9a3ca74d-3f5f-11ed-b552-960000d5cd75',
            'autoarticolato' => '9a3ca42e-3f5f-11ed-b552-960000d5cd75',
            'semirimorchio' => '9a3cbda9-3f5f-11ed-b552-960000d5cd75',
            'veicolo comunale' => '9a3ca6ce-3f5f-11ed-b552-960000d5cd75',
            'macchina agricola' => '9a3c9086-3f5f-11ed-b552-960000d5cd75',
            'macchina edile' => '9a3c9086-3f5f-11ed-b552-960000d5cd75',
            'bus' => '9a3ca7bc-3f5f-11ed-b552-960000d5cd75',
            'carrello elevatore' => '9a3cbbdf-3f5f-11ed-b552-960000d5cd75',
            'roulotte' => '3e465476-974c-45cd-a6be-65826535ea80',
            'casa mobile' => '3e465476-974c-45cd-a6be-65826535ea80',
        ];

        return $bodys_inventario[trim($body)];
    }
    public function storeAdImage($ad,$images)
    {
        $k = 0;

        foreach ($images as $image) {
            
            $ad_image = AdImage::query()
                ->where('ad_id', '=', $ad->id)
                ->where('path', '=', $image->large)
                ->first();

            if (is_null($ad_image)) {
                if ($k == 0) {
                    $ad->thumbnail = $image->large;
                    $ad->images_processing_status = 'SUCCESSFUL';
                    $ad->save();
                }else{
                    AdImage::create(['ad_id' => $ad->id,'path'=> $image->large, 'is_external' => 1, 'order_index' => $k]);   
                }
            }
            $k++;
        }
        
        $ad->images_processing_status = 'SUCCESSFUL';
        $ad->save();
    }
}
