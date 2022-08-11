<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use App\Models\{Ad,AutoAd,AdImage,MotoAd,MobileHomeAd,TruckAd,User,CarBodyType,Market,CarFuelType,CarTransmissionType,Dealer,DealerShowRoom,Make,Models};

class ImportWebmobile24AdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ads:webmobile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Webmobile24 ads import';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    private function getUsedMemory(): string
    {
        return sprintf("%sMB", intval(memory_get_usage(true) / 1024 / 1024));
    }

    private function getColorOptions(): array
    {
        $colors = [
            'rot' => 'red',     
            'grün' => 'green',
            'gelb' => 'yellow',
            'blau' => 'blue',
            'weiß' => 'white',
            'schwarz' => 'black',
            'orange' => 'oragen'  ,
            'lila' => 'lilac',
            'braun'  => 'brown' ,
            'rosa' => 'rose' ,
            'grau' => 'gray' ,
            'violett' => 'purple',
            'silbern' => 'silver'  ,
            'golden' => 'golden',
            'pink' => 'pink'
        ];

        return $colors;
    }


    private function getFuelOptions(): array
    {
        $fuel_types = [
            'Diesel' => 'diesel',
            'Elektro/Benzin' => 'hybrid_petrol_electric',
            'Benzin' => 'petrol',
            'Elektro' => 'electric',
            'LPG' => 'lpg', 
            //..  existen: 'Elektro/ Diesel'? CNG? Hidrógeno?..
        ];

        return $fuel_types;
    }

    private function getTransmissionOptions(): array
    {
        $transmissions = [
            'automatik' => "automatic", 
            'handschaltung' => "manual", 
            'halbautomatik' => "semi_automatic"
        ];

        return $transmissions;
    }

    private function getCondition(string $externalCondition): string
    {
        
        $conditions = [
            'Gebraucht' => "used", 
            'Jahreswagen' => "used", 
            'Tageszulassung' => "new", 
            'Vorführfahrzeug' => "new", 
            'Oldtimer' => "classic", 
            'Neu' => "new"
        ];

        if (isset($conditions[$externalCondition])) {
            return $conditions[$externalCondition];
        }

        return 'other';
    }

    

    private function getBodyOptions(): array
    {
        $bodys = [
            'Limousine' =>  ['internal_name' => 'limousine', 'ad_type' => 'AUTO'], //limousine
            'Van/Kleinbus' =>  ['internal_name' => 'minivan', 'ad_type' => 'TRUCK'], //van/minibus
            'Cabrio/Roadster' => ['internal_name' => 'convertible', 'ad_type' => 'AUTO'], //Convertible/Roadster
            'SUV/Geländewagen' => ['internal_name' => 'off_road_vehicle', 'ad_type' => 'AUTO'], //SUV/off-road vehicle
            'Kombi' =>  ['internal_name' => 'wagon', 'ad_type' => 'AUTO'], //station wagon
            'Sonstiges' => ['internal_name' => 'miscellaneous', 'ad_type' => 'AUTO'], //Miscellaneous
            'Kleinwagen'  => ['internal_name' => 'small_car', 'ad_type' => 'AUTO'], //small car
            'Sportwagen/Coupé' => ['internal_name' => 'sport_coupe', 'ad_type' => 'AUTO'], //sports car/coupe
            'Sonstige Moto' => ['internal_name' => 'other_moto', 'ad_type' => 'MOTO'], //Other Moto
            'Cabrio/Roadster' => ['internal_name' => 'convertible', 'ad_type' => 'AUTO'], //Convertible/Roadster
            'Lieferwagen' => ['internal_name' => 'deliverytrucks' , 'ad_type' => 'TRUCK'],  //delivery trucks
            'Wohnmobil sonstige' => ['internal_name' => 'wohnmobil_sonstige' , 'ad_type' => 'MOBILE-HOME'],
            
        ];

        return $bodys;
    }

    private function findFuelTypeId(string $externalFuel)
    {
        if ('' === $externalFuel) {
            return null;
        }
        //$externalFuel = strtolower(trim($externalFuel));
        $fuels        = $this->getFuelOptions();

        if (isset($fuels[$externalFuel])) {
            
            $car_fuel_type = CarFuelType::query()->where('internal_name', '=', $fuels[$externalFuel])
                              //->where('ad_type', '=', 'auto')
                              ->first();
        
            if (is_null($car_fuel_type)) {
                
                $this->info('Save new car fuel type: '.$externalFuel);
                
                $car_fuel_type = new CarFuelType;
                $car_fuel_type->internal_name = $externalFuel;
                $car_fuel_type->ad_type = 'AUTO';
                $car_fuel_type->slug = Str::slug($externalFuel);
                $car_fuel_type->save();
            }

            return $car_fuel_type;
        }

        $car_fuel_type = CarFuelType::query()->where('internal_name', '=', strtolower(trim($externalFuel)))
                              //->where('ad_type', '=', 'auto')
                              ->first();

        if (is_null($car_fuel_type)) {
                
            $this->info('Save new car fuel type: '.strtolower(trim($externalFuel)));
            
            $car_fuel_type = new CarFuelType;
            $car_fuel_type->internal_name = strtolower(trim($externalFuel));
            $car_fuel_type->ad_type = 'AUTO';
            $car_fuel_type->slug = Str::slug($externalFuel);
            $car_fuel_type->save();
        }

        return $car_fuel_type;
    }



    private function findBodyTypeId(string $externalBody)
    {
        
        if ('' === $externalBody) {
            return null;
        }
       
        $bodyTypes    = $this->getBodyOptions();
    
        if (isset($bodyTypes[trim($externalBody)])) {
            
            $car_body_type = CarBodyType::query()
                              ->where('internal_name', '=', $bodyTypes[trim($externalBody)]['internal_name'])
                              //->where('ad_type', '=', $bodyTypes[$externalBody]['ad_type'])
                              ->first();

           if (is_null($car_body_type)) {
                
                $this->info('Save new car body type: '.$bodyTypes[trim($externalBody)]['internal_name']);
                
                $car_body_type = new CarBodyType;
                $car_body_type->internal_name = $bodyTypes[trim($externalBody)]['internal_name'];
                $car_body_type->ad_type = $bodyTypes[trim($externalBody)]['ad_type'];
                $car_body_type->slug = Str::slug($bodyTypes[trim($externalBody)]['internal_name']);
                $car_body_type->save();
            }

            return $car_body_type;
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

        return null;
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
                                      ->where('ad_type', '=', 'auto')
                                      ->first();
            
            return $car_transmission_type;                       
        }

        return null;
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
            'VW' => 'Volkswagen'
        ];

        if (null === $make && isset($knownMakes[$externalMake])) {
            $make = Make::query()->where('name', '=', $knownMakes[$externalMake])->first();
        }

        if ($make instanceof Make) {
            return $make;
        }else{
            
            $make = new Make;
            
            $make->name = trim($externalMake);
            $make->slug = Str::slug($externalMake);
            $make->is_active = 1;  
            $make->ad_type = 'AUTO'; 

            $make->save(); 

            return $make;
        }

        throw new Exception(sprintf('invalid_make: %s', $externalMake));
    }

    private function findDealer(string $externalDealer): Dealer
    {
        if ('' === $externalDealer) {
            throw new Exception('no_dealer');
        }

        $externalDealer = strtolower(trim($externalDealer));

        $dealer = Dealer::query()
                    ->where('company_name', '=', $externalDealer)->first();

        if (is_null($dealer)) {
            
            $dealer = Dealer::create([
                    'slug' => Str::slug(trim($externalDealer)),
                    'company_name' => trim(strtoupper($externalDealer)),
                    'address' => '.',
                    'country' => 'Alemania',
                    'zip_code' => '.',
                    'city' => '.',
                    'email_address' => strtolower($externalDealer).'@autosmotos.es',
                    'phone_number' => '+00000000000',
                    'status' => 10,
                    'source' => 'WEB_MOBILE_24',
            ]);

            $this->info(sprintf('Successfully registered new dealer %s',$externalDealer));
        }

        return $dealer;

        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findDealerShowRoom(string $externalDealer,$dealer_id,$market_id): DealerShowRoom
    {
        if ('' === $externalDealer) {
            throw new Exception('no_dealer');
        }

        $externalDealer = strtoupper(trim($externalDealer));

        $dealer_show_room = DealerShowRoom::query()
                    ->where('name', '=', $externalDealer)->first();

        if (is_null($dealer_show_room)) {
            
            $dealer_show_room = DealerShowRoom::create([
                    'name' => trim(strtoupper($externalDealer)),
                    'address' => '.',
                    'city' => '.',
                    'zip_code' => '.',
                    'country' => 'Alemania',
                    'email_address' => strtolower($externalDealer).'@autosmotos.es',
                    'mobile_number' => '+00000000000',
                    'dealer_id' => $dealer_id,
                    'market_id' => $market_id
            ]);

            $this->info(sprintf('Successfully registered new dealer %s',$externalDealer));
        }

        return $dealer_show_room;

        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findUser(string $externalDealer,$dealer_id): User
    {
        if ('' === $externalDealer) {
            throw new Exception('no_user');
        }

        if ('' === $dealer_id) {
            throw new Exception('no_dealer_id');
        }

        $externalDealer = strtolower(trim($externalDealer));

        $user = User::query()
                    ->where('email', '=', strtolower($externalDealer).'@autosmotos.es')->first();

        if (is_null($user)) {
            
            $user = User::create([
                    'first_name' => trim(strtoupper($externalDealer)),
                    'last_name' => '.',
                    'email' => strtolower($externalDealer).'@autosmotos.es',
                    'password' => Hash::make(strtolower($externalDealer).'123**'),
                    'dealer_id' => $dealer_id,
                    'type' => 'Professional'
            ]);

            $this->info(sprintf('Successfully registered new user %s',$externalDealer));
        }

        return $user;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateAd($external_ad): Ad
    {
        if (count($external_ad) == 0) {
            throw new Exception('no_external_ad');
        }        

        $ad = Ad::where('slug',$external_ad['slug'])->first();
        
        if (is_null($ad)) {
            $ad = Ad::create($external_ad);

            $this->info(sprintf('Successfully registered new ad %s',$external_ad['external_id']));
        }
        
        return $ad;
    }

    private function findOrCreateAdImage($ad_id,$path,$order_index)
    {
        if ('' == $ad_id) {
            throw new Exception('no_external_ad');
        }

        $ad_image = AdImage::query()
                ->where('ad_id', '=', $ad_id)
                ->where('path', '=', $path)
                ->first();

        if (is_null($ad_image)) {
            
            $ad_image = AdImage::create([
                'ad_id' => $ad_id,
                'path' => $path, 
                'is_external' => 1 , 
                'order_index' => $order_index
            ]);

            $this->info(sprintf('Successfully registered new image in %s',$path));
            
            return false;
        }

        return $ad_image;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
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
        }

        return $moto_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateTruckAd($external_auto_ad,$ad): MotoAd
    {
        if (count($external_auto_ad) == 0) {
            throw new Exception('external_auto_ad');
        }

        $truck_ad = TruckAd::query()
                    ->where('ad_id', '=', $ad['id'])->first();

        if (is_null($truck_ad)) {
            
            $truck_ad = TruckAd::create($external_auto_ad);

            //$this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
        }

        return $truck_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateMobileHomeAd($external_auto_ad,$ad): MotoAd
    {
        if (count($external_auto_ad) == 0) {
            throw new Exception('external_auto_ad');
        }

        $mobile_home_ad = MobileHomeAd::query()
                    ->where('ad_id', '=', $ad['id'])->first();

        if (is_null($mobile_home_ad)) {
            
            $mobile_home_ad = MobileHomeAd::create($external_auto_ad);

            //$this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
        }

        return $mobile_home_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findModel(string $externalModel, Make $make): Models
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

            $slug = Models::whereIn('slug',[Str::slug($externalModel),Str::slug($externalModel.' '.$make->name)])->first();
            $model = new Models;
            $model->name = $externalModel;
            $model->slug = is_null($slug) ? Str::slug($externalModel) : Str::slug($externalModel.' '.$make->name);
            $model->make_id = $make->id;
            $model->ad_type = 'AUTO';
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
                        // ->where('ad_type', '=', 'auto')
                         ->where('make_id', '=', $makeId)
                         ->first();

        return $instance;
    }
    
    private function getMarket()
    {
        /** @var Model $instance */
        $market = Market::query()
                    ->where('internal_name', '=', 'germany')
                    ->first();

        return $market;
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

    public $totalAdsCounter = 0;
    public $totalAdsFailedCounter = 0;
    public $totalImageAdsCounter = 0;
    public $totalZipDecompressed = 0;
    public $totalZipNotDecompressed = 0;
    public $totalImageAdsImportedCounter = 0;
    public $totalImageAdsFailedCounter = 0;
    public $totalDealers = 0;
    
    public function handle()
    {
        $this->info(sprintf('Command started at %s', (new DateTime())->format('Y-m-d H:i:s')));

        $this->info('Accessing AWS Amazon .zip files');
        
        $zip_files = Storage::disk('ftp-s3')->files('webmobile24');
        
        //$zip_files = Storage::disk('local')->files('public/webmobile24');

        foreach ($zip_files as $key => $zip_file) {
            
            $zip = new ZipArchive;
            
            Storage::disk('local')->put($zip_file, Storage::disk('ftp-s3')->get($zip_file));
           
            $compressed = $zip->open(storage_path('app/'.$zip_file));

            
            if ($compressed === TRUE) {
                
                $directory = storage_path('app/');

                $zip->extractTo($directory.$key);

                $this->info(sprintf('The file %s was successfully decompressed! ',$zip_file));
                
                $this->totalZipDecompressed++;

                $handle = fopen($directory.$key.'/export.csv', "r");
                
                $this->info('Accessing to export.csv for import of ads');
               
                $i = 0;

                $zip_name = explode('/', $zip_file);

                $campany_name = explode('.', $zip_name[count($zip_name)-1]);
                
                $market = $this->getMarket();

                $dealer = $this->findDealer($campany_name[0]);

                $dealer_show_room = $this->findDealerShowRoom($campany_name[0],$dealer->id,$market->id);
                
                $user = $this->findUser($campany_name[0],$dealer->id);

                while (($csv_ad = fgetcsv($handle,null,';')) !== FALSE) {
                    
                    $this->totalAdsCounter++;
                    
                    try {

                        $thumbnail =  preg_split("/_/", $csv_ad[2]);
                           
                        $year_month = $csv_ad[13] !== null ? explode('.', $csv_ad[13]) : null;
                        
                        $thumbnail_format = explode('.', $csv_ad[2]);
                        
                        $images = Storage::disk('local')->files($key);
                        
                        $new_thumbnail;
                        $is_successful = false;

                        $this->totalImageAdsCounter = count($images)-1;

                        $body = $this->findBodyTypeId(utf8_encode($csv_ad[8]));
                        
                        $model =  $this->findModel(trim(utf8_encode($csv_ad[4])),$this->findMake(trim(utf8_encode($csv_ad[3]))));
                        
                        $external_id = explode('_', $csv_ad[2])[0];

                        $data_ad = [
                            'slug' => Str::slug(utf8_encode($csv_ad[5])) == '' ? $model->name : Str::slug(utf8_encode($csv_ad[5])),
                            'title' => utf8_encode($csv_ad[5]) == '' ? $model->name : utf8_encode($csv_ad[5]),
                            'description' => utf8_encode($csv_ad[58]),
                            'thumbnail' => '.',
                            'status' => 10,
                            'type' =>  $body == null ? '' : strtolower($body['ad_type']),
                            'user_id' => $user->id,
                            'market_id' => $market->id,
                            'external_id' => $external_id,
                            'source' => 'WEB_MOBILE_24',
                            'images_processing_status' => 'SUCCESSFUL'
                        ];

                        $vehicleAd = [
                            'price' => $csv_ad[15],//OK.
                            'price_contains_vat' => 0,
                            'vin' => null,
                            'doors' => trim($csv_ad[16]) == '' ? 0 : (int) $csv_ad[16], //OK
                            'mileage' => $csv_ad[14]== '' ? 0 : $csv_ad[14], ///OK
                            'exterior_color' => $this->getColor(trim(utf8_encode($csv_ad[9]))),
                            'color' => $this->getColor(trim(utf8_encode($csv_ad[9]))),
                            'interior_color' => $this->getColor(trim(utf8_encode($csv_ad[91]))),
                            'condition' =>  $this->getCondition(trim(utf8_encode($csv_ad[6]))), //OK
                            'dealer_id' => $dealer->id,
                            'dealer_show_room_id' => $dealer_show_room->id,
                            'email_address' => $dealer->email_address,
                            'address' => '.',
                            'zip_code' => '.',
                            'city' => '.',
                            'country' => 'Alemania',
                            'mobile_number' => '+000000000',
                            'ad_fuel_type_id' => $this->findFuelTypeId(trim(utf8_encode($csv_ad[12])))->id, //OK
                            'ad_body_type_id' => $body == null ? '' : $body['id'], //OK
                            'ad_transmission_type_id' => $this->findTransmissionTypeId(trim(utf8_encode($csv_ad[7])))->id, 
                            'fuel_type_id' => $this->findFuelTypeId(trim(utf8_encode($csv_ad[12])))->id, //OK
                            'body_type_id' => $body == null ? '' : $body['id'], //OK
                            'transmission_type_id' => $this->findTransmissionTypeId(trim(utf8_encode($csv_ad[7])))->id, 
                            'first_registration_year' => count($year_month) == 1 ? (string) date('Y')  : $year_month[1],
                            'first_registration_month' => count($year_month) == 1 ? (string) date('m') :  $year_month[0], 
                            'engine_displacement' => $csv_ad[18]  == '' ? 0 : $csv_ad[18], //OK
                            'power_hp' => $csv_ad[19], //OK
                            'make_id' => $this->findMake(trim(utf8_encode($csv_ad[3])))->id, //OK
                            'model_id' => $model->id, //OK
                            'additional_vehicle_info' => utf8_encode($csv_ad[5]), //OK
                            'seats' => $csv_ad[17], //OK
                        ];
                        
                        
                        $ad = $this->findOrCreateAd($data_ad);
                        
                        $vehicleAd['ad_id'] = $ad->id;

                        if ($body['ad_type'] == 'AUTO') {
                            $this->findOrCreateAutoAd($vehicleAd,$ad);
                        }

                        if ($body['ad_type'] == 'MOTO') {
                            $vehicleAd['vehicle_category_id'] ='8dc8cfab-ee22-4fe4-9246-0ada375eb4f8';
                            $this->findOrCreateMotoAd($vehicleAd,$ad);
                        }
                        
                        if ($body['ad_type'] == 'MOBILE-HOME') {
                            $vehicleAd['vehicle_category_id'] ='02d4cd46-6692-4c2b-9455-4683b961630d';
                            $this->findOrCreateMobileHomeAd($vehicleAd,$ad);
                        }
                        if ($body['ad_type'] == 'TRUCK') {
                            $vehicleAd['vehicle_category_id'] ='b0578de4-8c44-4ef9-ae74-cd736062f93a';
                            $this->findOrCreateTruckAd($vehicleAd,$ad);
                        }

                        $images = Storage::disk('local')->files($key);
                        
                        $i = 0;
                        
                        //$this->info($images );

                        foreach ($images as $image) {
                            $file = explode('/', $image);
                            $format = explode('.', $file[count($file)-1]);
                            

                            
                            //$this->info( var_dump($file));

                            if ($format[1] != 'csv'){
                                $thumbnail = preg_split("/_/",$file[count($file)-1]);
                                
                                //$this->info(var_dump($thumbnail));
                                
                                if ($thumbnail[0] == $external_id) {
                                    $i++;
                                    $directory = 'listings/'.$ad->id.'/'.$ad->id.'_'.$i.'.'.$format[1];
                                    
                                    $this->findOrCreateAdImage($ad->id,$directory,$i);

                                    if (!Storage::disk('s3')->exists($directory)) {
                                        
                                        if ($i == 1) {
                                            $ad->thumbnail = $directory;
                                            $ad->save();
                                        }
                                        
                                        $import = Storage::disk('s3')->put($directory,Storage::disk('local')->get($image));
                                        $import ? $this->totalImageAdsImportedCounter++ : $this->totalImageAdsFailedCounter++;
                                    }
                                }
                            } 
                        }

                    } catch (Exception $e) {
                        
                        $this->totalAdsFailedCounter++;

                        $this->error(
                            sprintf(
                                '==> Failed to load seller %s with error: %s; Line: %s,File: %s, RAM Used: %s',
                                $csv_ad[1],
                                $e->getMessage(),
                                $e->getLine(),
                                $e->getFile(),
                                $this->getUsedMemory()
                            )
                        );
                         ///$this->error($e->getPrevious());
                         //$this->error($e->getTrace());
                        continue;
                    }
                }
                
                $zip->close();

                Storage::disk('local')->delete($zip_file);
                
                $files = glob(storage_path('app/').$key.'/*'); //obtenemos todos los nombres de los ficheros
                
                foreach($files as $file){
                    if(is_file($file))
                        unlink($file); //elimino el fichero
                }

                rmdir(storage_path('app/').$key);

            } else {
                $this->totalZipNotDecompressed++;
                $this->error(sprintf('The file %s failed to decompress, reason: %s',$zip_file,$compressed));
            }
        }

        $this->info(
                sprintf(
                    '====> Total ads created: %d/%d of : %d/%d .zip; RAM Used: %s.',
                    $this->totalAdsCounter ,
                    $this->totalAdsCounter + $this->totalAdsFailedCounter,
                    $this->totalZipDecompressed,
                    $this->totalZipDecompressed+$this->totalZipNotDecompressed,
                    $this->getUsedMemory()
                )
            );

        $this->info(
                sprintf(
                    '====> Total image ads imported: %d/%d ; failed : %d RAM Used: %s.',
                    $this->totalImageAdsImportedCounter,
                    $this->totalImageAdsCounter,
                    $this->totalImageAdsFailedCounter,
                    $this->getUsedMemory()
                )
            );

        $this->info(sprintf('Command ended at %s', (new DateTime())->format('Y-m-d H:i:s')));
    }
}
