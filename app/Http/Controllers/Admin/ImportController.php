<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Carbon\Carbon as DateTime;
use Illuminate\Support\Str;
use Pusher\Pusher;
use Storage;
use App\Models\{Ad,AutoAd,AdImage,User,CarBodyType,CsvAd,Market,CarFuelType,CarTransmissionType,Dealer,DealerShowRoom,Make,Models};


class ImportController extends Controller
{   
    use ApiController;

    public function import_massive(Request $request)
    {
        ini_set('max_execution_time', 0);
        
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }
        
        try {

            $file = $request->file('file');
            
            $csv = date('dmyhms').'.csv';

            $path = $file->move(storage_path('app/massive'),$csv);
            
            $directory = storage_path('app/massive/'.$csv);

            $handle = fopen($directory, "r");

            $user = Auth::user();
            
            $is_admin = false;

            foreach ($user->roles as $role) {
                if ($role['name'] == 'ADMIN') {

                   $is_admin = true;
                }
            } 
             
            $csv_ad_id = $this->saveCsvAd($user['id'],$csv,$is_admin);

            $market = $this->getMarket();

            $dealer = $this->findDealer($user->dealer_id ?? null);

            $dealer_show_room = $this->findDealerShowRoom($dealer->id ?? null);
            
            $total_ads = 0;
            
            $count_ads = 0;
            
            $fh = fopen($directory,'rb') or die("ERROR OPENING DATA");

            while (fgets($fh) !== false) $total_ads++;

            fclose($fh);
            
            $i = 0;

            while (($csv_ad = fgetcsv($handle,null,';')) !== FALSE) {
                 if ($csv_ad[3] == 'make') {
                    continue;
                }

                $thumbnail =  preg_split("/_/", $csv_ad[2]);
                   
                $year_month = $csv_ad[13] !== null ? explode('.', $csv_ad[13]) : null;
                $until_year_month = $csv_ad[23] !== null ? explode('.', $csv_ad[23]) : null;
                
                $thumbnail_format = explode('.', $csv_ad[2]);                
                
                $external_id = $thumbnail[0];
                //23bcf97c-296b-46c9-bdd5-8057e052bfce el id de administrador actual

               
                $data_ad = [
                    'slug'                          => Str::slug(utf8_encode($csv_ad[5])),
                    'title'                         => utf8_encode($csv_ad[5]),
                    'description'                   => utf8_encode($csv_ad[57]) == '' ? utf8_encode($csv_ad[5])  :utf8_encode($csv_ad[57]),
                    'thumbnail'                     => NULL,
                    'status'                        => $is_admin ? 10 : 0,
                    'type'                          => 'auto',
                    'user_id'                       => $user->id,
                    'market_id'                     => $market->id,
                    'source'                        => 'CSV',
                    'images_processing_status'      => 'N/A',
                    'images_processing_status_text' => null,
                    'csv_ad_id'                     => $csv_ad_id['id']
                ];

                $data_auto_ad = [
                    'price'                    => $csv_ad[15],//OK.
                    'price_contains_vat'       => $csv_ad[10],
                    'vin'                      => null,
                    'doors'                    => $csv_ad[16] == '' ? 0 : $csv_ad[16], //OK
                    'mileage'                  => $csv_ad[14], ///OK
                    'exterior_color'           => utf8_encode($csv_ad[9]),
                    'interior_color'           => utf8_encode($csv_ad[90]),
                    'condition'                => $this->getCondition($csv_ad[6]), //OK
                    'dealer_id'                => !is_null($dealer) ? $dealer['id'] : null,
                    'dealer_show_room_id'      => !is_null($dealer_show_room)  ? $dealer_show_room['id'] : null,
                    'email_address'            => !is_null($dealer) ? $dealer['email_address'] : $user->email,
                    'address'                  => !is_null($dealer) ? $dealer['address'] : '.',
                    'zip_code'                 => !is_null($dealer) ? $dealer['zip_code'] : '.',
                    'city'                     => !is_null($dealer) ? $dealer['city'] : '.',
                    'country'                  => !is_null($dealer) ? $dealer['country'] : '.',
                    'mobile_number'            => !is_null($dealer) ? $dealer['phone_number'] : '.',
                    'ad_fuel_type_id'          => $this->findFuelTypeId($csv_ad[12])['id'] , //OK
                    'ad_body_type_id'          => $this->findBodyTypeId(utf8_encode($csv_ad[8]))['id'], //OK
                    'ad_transmission_type_id'  => $this->findTransmissionTypeId($csv_ad[7])['id'], 
                    'first_registration_year'  => $year_month[1] ?? '01',
                    'first_registration_month' => $year_month[0] ?? '2000',
                    'inspection_valid_until_year'  => $until_year_month[1] ?? null,
                    'inspection_valid_until_month' => $until_year_month[0] ?? null,
                    'engine_displacement'      => $csv_ad[18], //OK
                    'power_hp'                 => $csv_ad[19], //OK
                    'make_id'                  => $this->findMake($csv_ad[3])['id'], //OK
                    'model_id'                 => $this->findModel($csv_ad[4],$this->findMake($csv_ad[3]))['id'], //OK
                    'additional_vehicle_info'  => utf8_encode($csv_ad[57]), //OK
                    'seats'                    => $csv_ad[17], //OK
                ];
                

                $ad = $this->findOrCreateAd($data_ad);
                
                $data_auto_ad['ad_id'] = $ad->id;
                
                $auto_ad = $this->findOrCreateAutoAd($data_auto_ad,$ad);
                $count_ads++;    

                /*$pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    [ 
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true
                    ]
                );

                $pusher->trigger(
                    'percentage',
                    'percentage-'.$user['id'],
                    [ 
                        'percentage' => round(($count_ads*100)/$total_ads,2).'%'
                    ]
                );   */  
                $i++;
            }
        
        } catch (Exception $e) {
            Ad::where('csv_ad_id',$csv_ad_id['id'])->whereRaw('id not in(SELECT ad_id from auto_ads)')->delete();  
            
            
            $resource = array_merge($resource, [
                    'error_message' => sprintf('Error en la fila %d por favor verifique el archivo e intente  nuevamente',$count_ads+1) 
                ]
            );
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        } 
        
        $resource = array_merge($resource, [
                    'data' => 'Total de anuncios importandos '.$count_ads.' de '.($total_ads-1)
                ]
            );

        ApiHelper::success($resource);

        return $this->sendResponse($resource);  
    }

    private function getColorOptions(): array
    {
        $colors = [
            'rot'           => 'red',     
            'grün'          => 'green',
            'gelb'          => 'yellow',
            'blau'          => 'blue',
            'weiß'          => 'white',
            'schwarz'       => 'black',
            'orange'        => 'oragen',
            'lila'          => 'lilac',
            'braun'         => 'brown',
            'rosa'          => 'rose',
            'grau'          => 'gray',
            'violett'       => 'purple',
            'silbern'       => 'silver',
            'golden'        => 'golden',
            'pink'          => 'pink',
            'red'           => 'red',     
            'green'         => 'green',
            'yellow'        => 'yellow',
            'blue'          => 'blue',
            'white'         => 'white',
            'black'         => 'black',
            'oragen'        => 'oragen',
            'lilac'         => 'lilac',
            'brown'         => 'brown',
            'rose'          => 'rose',
            'gray'          => 'gray',
            'purple'        => 'purple',
            'silver'        => 'silver',
            'golden'        => 'golden',
            'pink'          => 'pink',
            'rojo'          => 'red',
            'azul'          => 'blue',
            'plateado'      => 'silver',
            'blanco'        => 'white',
            'negro'         => 'black',
            'marrón'        => 'brown',
            'gris'          => 'gray',
            'oro'           => 'gold',
            'verde'         => 'green',
            'beige'         => 'beige',
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

        return $colors;
    }


    private function getFuelOptions(): array
    {
        $fuel_types = [
            'diesel'            => 'diesel',
            'elektro/benzin'    => 'hybrid_petrol_electric',
            'benzin'            => 'petrol',
            'elektro'           => 'electric',
            'lpg'               => 'lpg', 
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
            'benzina'           => 'petrol',
            'diesel'            => 'diesel',
            'gpl'               => 'gas_gasoline',
            'solo gpl'          => 'gas_gasoline',
            'elettrica'         => 'electric',
            'elettrica-benzina' => 'hybrid_petrol_electric',
            'elettrica-diesel'  => 'hybrid_diesel_electric',
            'altro'             => 'other',
            'metano'            => 'other',
            'diésel'                    => 'diesel',
            'eléctrico'                 => 'electric',
            'gas'                       => 'gas_gasoline',
            'gasolina'                  => 'gas_gasoline',
            'híbrido (gasolina)'        => 'hybrid_petrol_electric',
            'híbrido (diésel)'          => 'hybrid_diesel_electric',
            'híbrido'                   => 'other',
            'etanol'                    => 'ethanol',
            'híbrido enchufable (phev)' => 'other',
            //..  existen: 'Elektro/ Diesel'? CNG? Hidrógeno?..
        ];

        return $fuel_types;
    }

    private function getTransmissionOptions(): array
    {
        $transmissions = [
            'automatik' => "automatic", 
            'handschaltung' => "manual", 
            'halbautomatik' => "semi_automatic",
            'manual'     => 'manual',
            'automático' => 'automatic',
            'manuale'    => 'manual',
            'automatico' => 'automatic',
            'automatic' => 'automatic',
        ];

        return $transmissions;
    }

    private function getCondition(string $externalCondition): string
    {
        
        $conditions = [
            'gebraucht' => "used", 
            'new' => "new", 
            'usado' => "used", 
            'used' => "used", 
            'nuevo' => "new", 
            'oldtimer' => "classic", 
            'neu' => "new",
            'kmzero'  => 'new',
            'nuovo'   => 'new',
            'ocasion' => 'used',
            'usato'   => 'used',
        ];

        if (isset($conditions[trim(strtolower($externalCondition))])) {
            return $conditions[trim(strtolower($externalCondition))];
        }

        return 'other';
    }

    

    private function getBodyOptions(): array
    {
        $bodys = [
            'Limousine'       => ['internal_name' => 'limousine', 'ad_type' => 'AUTO'], //limousine
            'Van/Kleinbus'    => ['internal_name' => 'minivan', 'ad_type' => 'AUTO'], //van/minibus
            'Cabrio/Roadster' => ['internal_name' => 'convertible', 'ad_type' => 'AUTO'], //Convertible/Roadster
            'SUV/Geländewagen'=> ['internal_name' => 'off_road_vehicle', 'ad_type' => 'AUTO'], //SUV/off-road vehicle
            'Kombi'           => ['internal_name' => 'wagon', 'ad_type' => 'AUTO'], //station wagon
            'Sonstiges'       => ['internal_name' => 'miscellaneous', 'ad_type' => 'AUTO'], //Miscellaneous
            'Kleinwagen'      => ['internal_name' => 'small_car', 'ad_type' => 'AUTO'], //small car
            'Sportwagen/Coupé'=> ['internal_name' => 'sport_coupe', 'ad_type' => 'AUTO'], //sports car/coupe
            'Sonstige Moto'   => ['internal_name' => 'other_moto', 'ad_type' => 'MOTO'], //Other Moto
            'Cabrio/Roadster' => ['internal_name' => 'convertible', 'ad_type' => 'AUTO'], //Convertible/Roadster
            'Lieferwagen'     => ['internal_name' => 'deliverytrucks' , 'ad_type' => 'AUTO'],  //delivery trucks
            'sedan'           => ['internal_name' => 'sedan', 'ad_type' => 'AUTO'],
            'bus'             => ['internal_name' => null, 'ad_type' => 'AUTO'],
            'cabrio'          => ['internal_name' => 'convertible', 'ad_type' => 'AUTO'],
            'coupè'           => ['internal_name' => 'sport_coupe', 'ad_type' => 'AUTO'],
            '4 X 4'           => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'transporter'     => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'furgoneta'       => ['internal_name' => 'minivan', 'ad_type' => 'AUTO'],
            'otro'            => ['internal_name' => null, 'ad_type' => 'AUTO'],
            'station wagon'   => ['internal_name' => 'wagon', 'ad_type' => 'AUTO'],
            'monovolumen'     => ['internal_name' => 'minivan', 'ad_type' => 'AUTO'],
            'suv'             => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'pick up'         => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'sin licencia'    => ['internal_name' => null, 'ad_type' => 'AUTO'],
            'dos volúmenes'   => ['internal_name' => 'hatchback', 'ad_type' => 'AUTO'],
            'coche pequeño'   => ['internal_name' => 'hatchback', 'ad_type' => 'AUTO'],
            'city car'        => ['internal_name' => 'hatchback', 'ad_type' => 'AUTO'],
            'utility car'     => ['internal_name' => null, 'ad_type' => 'AUTO'],
            'sport'           => ['internal_name' => 'sport_coupe', 'ad_type' => 'AUTO'],
            '4x4 todoterreno' => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'pick up 4x4'     => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'turismo'         => ['internal_name' => null, 'ad_type' => 'AUTO'],
            'berlina'         => ['internal_name' => 'sedan', 'ad_type' => 'AUTO'],
            'tres volúmenes'  => ['internal_name' => 'wagon', 'ad_type' => 'AUTO'],
            'cabriolet'           => ['internal_name' => 'convertible', 'ad_type' => 'AUTO'],
            'coche sin carnet'    => ['internal_name' => null, 'ad_type' => 'AUTO'],
            'deportivo'           => ['internal_name' => 'sport_coupe', 'ad_type' => 'AUTO'],
            'familiar'            => ['internal_name' => 'minivan', 'ad_type' => 'AUTO'],
            'pickup'              => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'todoterreno'         => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'utilitario'          => ['internal_name' => null, 'ad_type' => 'AUTO'],
            'vehículo industrial' => ['internal_name' => null, 'ad_type' => 'AUTO'],
            '4x4 SUV'             => ['internal_name' => 'suv_crossover', 'ad_type' => 'AUTO'],
            'coupé'               => ['internal_name' => 'sport_coupe', 'ad_type' => 'AUTO']
        ];

        return $bodys;
    }

    private function saveCsvAd($user_id,$csv,$is_admin)
    {  
        $status = 'Pendiente';
        
        if ($is_admin) {
            $status = 'Aprobado';
        }

        $csv = CsvAd::create(['user_id' => $user_id , 'name' => $csv, 'status' =>  $status]);
        
        return $csv;
    }

    private function findFuelTypeId(string $externalFuel)
    {
        if ('' === $externalFuel) {
            return null;
        }
        $externalFuel = strtolower(trim($externalFuel));
        $fuels        = $this->getFuelOptions();

        if (isset($fuels[$externalFuel])) {
            
            $car_fuel_type = CarFuelType::query()->where('internal_name', '=', $fuels[$externalFuel])
                              ->first();
        
            if (is_null($car_fuel_type)) {
                //$this->info($externalFuel.' '.strtolower(trim($externalFuel)).'LINE 166');
                $car_fuel_type['id'] = 'ed20075a-5297-11eb-b5ca-02e7c1e23b94';
            }
            
            return $car_fuel_type;
        }

        $car_fuel_type = CarFuelType::query()->where('internal_name', '=', $externalFuel)
                              ->first();

        if (is_null($car_fuel_type)) {
            //$this->info($car_fuel_type.' '.strtolower(trim($externalFuel)).'LINE 177');
            $car_fuel_type['id'] = 'ed20075a-5297-11eb-b5ca-02e7c1e23b94';
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
                              ->first();

           if (is_null($car_body_type)) {
                 $car_body_type['id'] = null;
            }

           
            return $car_body_type;
        }

        $car_body_type['id'] = null;
        return $car_body_type;
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
            
            return $car_transmission_type;                       
        }

        return null;
    }
    

    private function findMake(string $externalMake): Make
    {
        if ('' === $externalMake) {
            return null;
        }
        $externalMake = strtolower(trim($externalMake));

        $make = Make::query()
                    ->where('ad_type', '=', 'auto')
                    ->where('name', '=', $externalMake)
                    ->first();

        $knownMakes = [
            'mercedes'    => 'mercedes-benz',
            'rolls royce' => 'rolls-royce',
            'VW' => 'Volkswagen',
            'vw' => 'Volkswagen'
        ];

        if (null === $make && isset($knownMakes[$externalMake])) {
            $make = Make::query()
                        ->where('name', '=', $knownMakes[$externalMake])
                        ->where('ad_type', '=', 'auto')
                        ->first();
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

    private function findDealer($dealer_id)
    {
        if (null === $dealer_id) {
            return null;
        }

        $dealer = Dealer::query()
                    ->where('id', '=', $dealer_id)->first();

        return $dealer;
    }

    private function findDealerShowRoom($dealer_id)
    {
        if (null === $dealer_id) {
            return null;
        }

        $dealer_show_room = DealerShowRoom::query()
                    ->where('dealer_id', '=', $dealer_id)->first();

        return $dealer_show_room;
    }

    private function findOrCreateAd($external_ad)
    {   
        $validate = Ad::where('slug',$external_ad['slug'])->count();  
        
        if ($validate  != 0) {
            $external_ad['slug'] .= '-'.Str::uuid()->toString().'-'.$validate;
        }
        
        $ad = Ad::create($external_ad);

        return $ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findOrCreateAdImage($ad_id,$path,$order_index)
    {
        if ('' == $ad_id) {
            return null;
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

            //$this->info(sprintf('Successfully registered new image in %s',$path));
            
            return false;
        }

        return $ad_image;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }


    private function findOrCreateAutoAd($external_auto_ad,$ad)
    {
        if (count($external_auto_ad) == 0) {
            return null;
        }

        $auto_ad = AutoAd::query()
                    ->where('ad_id', '=', $ad['id'])->first();

        if (is_null($auto_ad)) {
            
            $auto_ad = AutoAd::create($external_auto_ad);

            //$this->info(sprintf('Successfully registered new auto_ad %s',$ad['external_id']));
        }

        $auto_ad->update($external_auto_ad);

        return $auto_ad;
        //throw new Exception(sprintf('invalid_dea: %s', $externalMake));
    }

    private function findModel(string $externalModel, Make $make): Models
    {
        if ('' === $externalModel) {
           return null;
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

            //$this->info(sprintf('Save new External Model: %s , Mark: %s', $externalModel ,$make->name));

            $slug = Models::where('slug','=',Str::slug($externalModel))->first();
            $model = new Models;
            $model->name = $externalModel;
            $model->slug = is_null($slug) ? Str::slug($externalModel) : Str::slug($externalModel.' '.$make->name);
            $model->make_id = $make->id;
            $model->ad_type = 'AUTO';
            $model->external_updated_at = DateTime::now();
            $model->save();

            return $model;
        }



        //throw new Exception(sprintf('invalid_model for make %s: %s', $make->name, $externalModel));
    }

    private function queryModel(string $name, string $makeId)
    {
        /** @var Model $instance */
        $instance = Models::query()->where('name', '=', $name)
                         ->where('ad_type', '=', 'auto')
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

    public function downloadCsv()
    {
        return Storage::download('files/planilla.csv');
    }

}
