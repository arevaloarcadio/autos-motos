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
use Illuminate\Support\Facades\Hash;
use App\Manager\Market\MarketManager;
use App\Models\Ad;
use App\Models\MotoAd;
use App\Models\TruckAd;
use App\Models\MobileHomeAd;
use App\Models\AutoAd;
use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarTransmissionType;
use App\Models\Make;
use App\Models\AdImage;
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
        //$filePath = $this->saveXmlLocally();

        $xml      = simplexml_load_file(env('INVENTARIO_IMPORT_URL'));

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
                $user     = $this->findUser($seller,$dealer->id);
                
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
                if ( $ad->id == '') {
                    continue;
                }

                
                $totalAdsCounter++;
                $externalId       = (int) $ad->id;
                $importedAdsIds[] = $externalId;
                $typeAd = $this->getTypeAd($ad->carroceria); 

                /*$existingAd = Ad::query()
                                ->where('type', '=', $typeAd)
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
                }*/
                try {
                    $startTime = microtime(true);
                    $this->createAd(
                        $ad,
                        $user,
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
                            '====> Ad %d errored with message: %s;LINE %s : %d/%d; RAM Used: %s',
                            $externalId,
                            $exception->getMessage(),
                            $exception->getLine(),
                            $counter + 1,
                            $totalAds,
                            $this->getUsedMemory()
                        )
                    );
                    $counter++;
                    continue;
                }
            }

//            $this->cleanUpAds($dealer, $importedAdsIds);
            // delete external ads from this dealer not in $importedAdsIds;
        }

  //      $this->cleanUpDealers($importedSellersIds);
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


    private function findUser(SimpleXMLElement $externalDealer,$dealer_id): User
    {
        if ('' === $externalDealer) {
            throw new Exception('no_user');
        }

        if ('' === $dealer_id) {
            throw new Exception('no_dealer_id');
        }
        

        $user = User::query()
                    ->where('dealer_id', '=', $dealer_id)->first();

        if (is_null($user)) {
            
            $user = User::create([
                    'first_name' => trim($externalDealer->cliente_nombre),
                    'last_name' => '.',
                    'email' =>   strtolower(trim($externalDealer->cliente_email)),
                    'password' => Hash::make($externalDealer->cliente_email.'123**'),
                    'dealer_id' => $dealer_id,
                    'type' => 'Profesional'
            ]);

            $this->info(sprintf('Successfully registered new user %s',$externalDealer));
        }

        return $user;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }



    private function findOrCreateDealer(SimpleXMLElement $seller): Dealer
    {
        $vatNumber = (string) $seller->cliente_cif === '' ? null : (string) $seller->cliente_cif;
        $dealer    = Dealer::query()
                           ->where('vat_number', '=', $vatNumber)
                           ->where('slug', '=', Str::slug((string) $seller->cliente_nombre))
                           ->first();
        $this->info( var_dump($seller->cliente_logo));

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

    private function findFuelTypeId(string $externalFuel)
    {
        if ('' === $externalFuel) {
            return null;
        }
        //$externalFuel = strtolower(trim($externalFuel));
        $fuels        = $this->getFuelOptions();

        if (isset($fuels[trim($externalFuel)])) {
            
            $this->info($fuels[trim($externalFuel)].' '.strtolower(trim($externalFuel)).'LINE 160');
            
            $car_fuel_type = CarFuelType::query()->where('internal_name', '=', $fuels[trim($externalFuel)])
                              ->first();
        
            if (is_null($car_fuel_type)) {
                //$this->info($externalFuel.' '.strtolower(trim($externalFuel)).'LINE 166');
                $car_fuel_type['id'] = 'ed20075a-5297-11eb-b5ca-02e7c1e23b94';
            }
            
            return $car_fuel_type['id'];
        }

        $car_fuel_type = CarFuelType::query()->where('internal_name', '=', strtolower(trim($externalFuel)))
                              ->first();

        if (is_null($car_fuel_type)) {
            //$this->info($car_fuel_type.' '.strtolower(trim($externalFuel)).'LINE 177');
            $car_fuel_type['id'] = 'ed20075a-5297-11eb-b5ca-02e7c1e23b94';
        }

        return $car_fuel_type['id'];
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

    private function findBodyTypeId(string $externalBody)
    {
        
        if ('' === $externalBody) {
            return null;
        }
       
        $bodyTypes    = $this->getBodyOptions();
    
        if (isset($bodyTypes[trim($externalBody)])) {
            
            $car_body_type = CarBodyType::query()
                              ->where('internal_name', '=', $bodyTypes[trim($externalBody)])
                              //->where('ad_type', '=', $bodyTypes[$externalBody]['ad_type'])
                              ->first();

           if (is_null($car_body_type)) {
                $car_body_type['id'] = null;
                /*$this->info('Save new car body type: '.$bodyTypes[trim($externalBody)]['internal_name']);
                
                $car_body_type = new CarBodyType;
                $car_body_type->internal_name = $bodyTypes[trim($externalBody)]['internal_name'];
                $car_body_type->ad_type =  $bodyTypes[trim($externalBody)]['ad_type'];
                $car_body_type->slug = Str::slug($bodyTypes[trim($externalBody)]['internal_name']);
                $car_body_type->save();*/
            }

            
            return $car_body_type['id'];
        }

        /*$car_body_type = CarBodyType::query()
                              ->where('internal_name', '=', strtolower(trim($externalBody)))
                              //->where('ad_type', '=', 'AUTO')
                              ->first();

        if (is_null($car_body_type)) {
            
            $this->info('Save new car body type: '.strtolower(trim($externalBody)));
            
            $car_body_type = new CarBodyType;
            $car_body_type->internal_name = strtolower(trim($externalBody));
            $car_body_type->ad_type = 'AUTO';
            $car_body_type->slug = Str::slug($externalBody); 
            $car_body_type->save();
        }*/
        $car_body_type['id'] = null;
        return $car_body_type['id'];
    }

    private function getBodyOptions(): array
    {
        return [
            'Berlina'             => 'sedan',
            'Cabriolet'           => 'convertible',
            'Coche sin carnet'    =>'sedan',
            'Deportivo'           => 'sport_coupe',
            'Familiar'            => 'minivan',
            'Furgoneta'           => 'minivan',
            'Monovolumen'         => 'minivan',
            'Pickup'              => 'suv_crossover',
            'Sedan'               => 'sedan',
            'Todoterreno'         => 'suv_crossover',
            'Utilitario'          => 'sedan',
            'Vehículo industrial' => 'deliverytrucks',
        ];
    }

    private function findTransmissionTypeId(string $externalTransmission)
    {
        if ('' === $externalTransmission) {
            return null;
        }
        $externalTransmission = strtolower(trim($externalTransmission));
        $transmissions        = $this->getTransmissionOptions();
       
        if (isset($transmissions[$externalTransmission])) {
            $car_transmission_type = CarTransmissionType::query()
                                      ->where('internal_name', '=', $transmissions[$externalTransmission])
                                     // ->where('ad_type', '=', 'auto')
                                      ->first();
            
            return $car_transmission_type['id'];                    
        }
        
        $car_transmission_type['id'] = null;

        return $car_transmission_type['id'];
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
                    //->where('ad_type', '=', 'auto')
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

    private function queryModel(string $name, string $makeId): ?Models
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

        $typeAd = $this->getTypeAd($ad->carroceria); 
        
        $this->info($typeAd);
        $this->info($ad->carroceria);

        $key = '';

        if($typeAd == 'auto'){
            $key = 'autoAd';   
        }

        if($typeAd == 'mobile-home'){
           $key = 'mobileHomeAd';
        }

        if($typeAd == 'truck'){
            $key = 'truckAd';
        }

        /*if ($existingAd[$key]->updated_at >= Carbon::parse((string) $ad->fch_modificacion)) {
            $skippedAdsCounter++;
            $this->info(
                sprintf(
                    '======> Skipped ad; RAM Used: %s',
                    $this->getUsedMemory()
                )
            );

            return false;
        }*/
        
        $changed = false;
        
        if ($existingAd[$key] == null) {
            return false;
        }
        
        if (null === $existingAd[$key]->transmissionType) {
            $existingAd[$key][$key !='autoAd' ? 'transmission_type_id' : 'ad_transmission_type_id']  = $this->findTransmissionTypeId((string) $ad->cambio);
            $changed                                     = true;
        }

        if ($key == 'autoAd') {
            if (null === $existingAd[$key]->bodyType) {
                $existingAd[$key][$key !='autoAd' ? 'body_type_id' : 'ad_body_type_id']  = $this->findBodyTypeId((string) $ad->carroceria);
                $changed                             = true;
            }
        }
        
        if (null === $existingAd[$key]->fuelType) {
            $existingAd[$key][$key !='autoAd' ? 'fuel_type_id' : 'ad_fuel_type_id'] = $this->findFuelTypeId((string) $ad->combustible);
            $changed                             = true;
        }
        
        if ('other' === $existingAd[$key]->exterior_color) {
            $existingAd[$key]->exterior_color = $this->getColor((string) $ad->color);
            $changed                            = true;
        }
        $price            = (float) $ad->precio;
        $priceContainsVat = (string) $ad->iva_deducible === 'True' ? true : false;
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

            $ad = Ad::where('description',$external_ad['description'])
                //->where('dealer_id',$dealer_id)
                ->first();
            
            if (is_null($ad)) {

                $external_ad['slug'] .= random_int(1000, 9999);
                
                $ad = Ad::create($external_ad);

                $this->info(sprintf('2: Successfully registered new %s, %s',$external_ad['type'],$external_ad['external_id']));
            }

            $ad->update($external_ad);
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
            //$this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
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

            //$this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
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

            //$this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
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

            //$this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
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
        Dealer $dealer,
        DealerShowRoom $showRoom
    ){
        $title            = $this->generateAdditionalVehicleInfo($adInfo);
        $description      = (string) $adInfo->descripcion;
        $registrationDate = $this->processRegistrationDate(
            (string) $adInfo->ano_matriculacion,
            (string) $adInfo->mes_matriculacion
        );
        $make             = $this->findMake((string) $adInfo->marca);
        $typeAd = $this->getTypeAd($adInfo->carroceria); 
        $model            = $this->findModel((string) $adInfo->modelo, $make,strtoupper($typeAd));
       
       $this->info($typeAd);
        $this->info($adInfo->carroceria);

        $adInput          = [
            'title'                    => $title,
            'description'              => $description,
            'status'                   => ApprovalStatusEnum::APPROVED,
            'user_id'                  => $user->id,
            'market_id'                => $marketId,
            'source'                   => AdSourceEnum::INVENTARIO_IMPORT,
            'external_id'              => $externalId,
            'slug'                     => Str::slug($title),
            'images'                   => [],
            'images_processing_status' => ImageProcessingStatusEnum::PENDING,
            'type' =>  $typeAd,
        ];

        $vehicleAd = [
            'price'                        => (float) $adInfo->precio,
            'price_contains_vat'           => (string) $adInfo->iva_deducible === 'True' ? true : false,
            'vin'                          => null,
            'doors'                        => $adInfo->puertas == '' ? null : $this->formatIntValue((string) $adInfo->puertas),
            'seats'                        => $adInfo->asientos == '' ? null : $this->formatIntValue((string) $adInfo->asientos),
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
            'fuel_type_id'                 => $this->findFuelTypeId((string) $adInfo->combustible),
            'body_type_id'                 => $this->findBodyTypeId((string) $adInfo->carroceria),
            'transmission_type_id'         => $this->findTransmissionTypeId((string) $adInfo->cambio),
            'drive_type_id'                => null,
            'first_registration_month'     => $registrationDate instanceof Carbon ? $registrationDate->month : null,
            'first_registration_year'      => $registrationDate instanceof Carbon ? $registrationDate->year : null,
            'engine_displacement'          => $adInfo->potencia == '' ? null : $this->formatIntValue((string) $adInfo->cilindrada),
            'power_hp'                     => $adInfo->potencia == '' ? null : $this->formatIntValue((string) $adInfo->potencia),
            'owners'                       => $adInfo->duenos_anteriores == '' ? null : $this->formatIntValue((string) $adInfo->duenos_anteriores),
            'inspection_valid_until_month' => null,
            'inspection_valid_until_year'  => null,
            'truck_type' => $adInfo->carroceria,
            'make_id'                      => $make->id,
            'model_id'                     => $model->id,
            'generation_id'                => null,
            'series_id'                    => null,
            'trim_id'                      => null,
            'equipment_id'                 => null,
            'additional_vehicle_info'      => $this->generateAdditionalVehicleInfo($adInfo),
            'co2_emission'                 => $this->formatFloatValue((string) $adInfo->emisiones_combinadas),
            'options'                      => [],
            'vehicle_category_id'          => '9a3cdc80-3f5f-11ed-b552-960000d5cd75',
        ];
       
        
        try {
               
              
            if($typeAd == 'auto'){
                
                $ad = $this->findOrCreateAd($adInput,$dealer->id);
                    
                $vehicleAd['ad_id'] = $ad->id;
                $this->storeAdImage($ad,$adInfo->imagenes->imagen);
           
                return $this->findOrCreateAutoAd($vehicleAd,$ad);
            }

           
            if($typeAd == 'truck'){
                $ad = $this->findOrCreateAd($adInput,$dealer->id);
                    
                $vehicleAd['ad_id'] = $ad->id;
                $vehicleAd['vehicle_category_id'] ='9a3cdc80-3f5f-11ed-b552-960000d5cd75';
                
                $this->storeAdImage($ad,$adInfo->imagenes->imagen);
             
                return $this->findOrCreateTruckAd($vehicleAd,$ad);
            }

        } catch (Exception $e) {
            
            $this->error(
                sprintf(
                    '==>Error: %s , %s, %s ,%s',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile(),
                    $this->getUsedMemory()
                )
            );

            \Illuminate\Support\Facades\Log::build(['driver' => 'single', 'path' => storage_path('logs/invetario_'.date('dmy').'.log')])->debug(sprintf(
                    '==>Error: %s , %s, %s ,%s',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile(),
                    $this->getUsedMemory()
                ));

        }
        /*$images = $adInfo->imagenes;
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

        return $this->adCreator->create(AdTypeEnum::AUTO_SLUG, $adInput);*/
    }


    public function storeAdImage($ad,$images)
    {
        $k = 0;

        foreach ($images as $image) {
            
            $ad_image = AdImage::query()
                ->where('ad_id', '=', $ad->id)
                ->where('path', '=', $image->imagen_url)
                ->first();

            if (is_null($ad_image)) {
                if ($k == 0) {
                    $ad->thumbnail = $image->imagen_url;
                    $ad->images_processing_status = 'SUCCESSFUL';
                    $ad->save();
                }else{
                    AdImage::create(['ad_id' => $ad->id,'path'=> $image->imagen_url, 'is_external' => 1, 'order_index' => $k]);   
                }
            }
            $k++;
        }
        
        $ad->images_processing_status = 'SUCCESSFUL';
        $ad->save();
    }
    
    public function getTypeAd($body)
    {   
        $bodys_inventario = [
            'Berlina' => 'auto',
            'Cabriolet' => 'auto',
            'Deportivo' => 'auto',
            'Coche sin carnet'    => 'auto',
            'Familiar' => 'auto',
            'Furgoneta' => 'truck',
            'Monovolumen' => 'auto',
            'Pickup' => 'auto',
            'Sedan' => 'auto',
            'Todoterreno' => 'auto',
            'Utilitario' => 'auto',
            'Vehículo Industrial' => 'truck',
        ];

        return $bodys_inventario[trim($body)];
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
