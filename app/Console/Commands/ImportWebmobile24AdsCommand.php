<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Log;


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
    protected $description = 'Portal club ads import';

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
    public function handle()
    {
        $this->info(sprintf('Command started at %s', (new DateTime())->format('Y-m-d H:i:s')));

        //LEEME: Poner el directorio webmobile24 en la raíz
        //  --/webmobile24/
        //  -----/honke/
        //  -----/hallierclassic/
        //  -----/daihatsu-20515/
        //  -----/auto-sommerfeld/
        //  -----/logo-us/
        //  -----/pewegmbh/
        //  -----/suzuki-de31800/

        $totalAdsCounter = 0;
        $totalSellerCounter = 0;
        $totalAdsRepetidos = 0;
        $arraySellers = [];

        $this->info('Accessing AWS Amazon .zip files');
        
        $zip_files = Storage::disk('ftp-s3')->files('webmobile24');
        
        foreach ($zip_files as $key => $zip_file) {
            $zip = new ZipArchive;

            $compressed = $zip->open(Storage::disk('ftp-s3')->get(sprintf('%s',$zip_file)));

            if ($compressed === TRUE) {
                $zip->extractTo('webmobile24_'.$zip_file.'/');
                $zip->close();
                 
                $this->info(sprintf('The file %s was successfully decompressed! ',$zip_file));
                
            } else {
                $this->error(sprintf('The file %s failed to decompress, reason: %s',$zip_file,$compressed));
            }
            die();
        }


        


        /*
        $files      = Storage::disk('ftp-s3')->files('webmobile24');
        $totalFiles = count($files);
        //exit(1);

        foreach($files as $file){

            //if($file != "webmobile24/hallierclassic.zip"){
            //    continue;
            //}

            $fileContent = Storage::disk('ftp-s3')->get($file);
            $local = Storage::disk('local');
            $local->put("./".$file, $fileContent);

            $zip = new ZipArchive();

            //Path of the source zip file to be extracted
            $file_all =  preg_split("/\./", $file);
            $filename = $file_all[0];
            //Get extension
            $extension = $file_all[1];

            $source = "public/".$file;
            $this->info($source);

            if ($zip->open($source) === TRUE) {
              //Destination of extracted files and folders to be stored
              $destination = $filename;
              $zip->extractTo($destination);
              $zip->close();
            } else {
              echo "Failed to open the zip file!";
            }

            //exit(1);
        }
        $this->info(sprintf('Total files: %d', $totalFiles));

        exit(1);
        */


//
        //$dir = "webmobile24/";
//
        //// Abre un directorio conocido, y procede a leer el contenido
        //if (is_dir($dir)) {
        //    if ($dh = opendir($dir)) {
        //        while (($file = readdir($dh)) !== false) {
//
        //            if($file != "." && $file != ".."){
        //                echo "nombre archivo: $file : tipo archivo: " . filetype($dir . $file) . "\n";
        //                if(is_dir($dir.$file)){
//
        //                    if ($dh1 = opendir($dir.$file)) {
        //                        while (($file2 = readdir($dh1)) !== false) {
        //                            //echo filetype($file2) . " \n";
        //                            if(pathinfo($file2, PATHINFO_EXTENSION)== "csv"){
        //                                //Get CSV file name
        //                                echo "file csv: " . $dir.$file."/".$file2 . "\n";
        //                            }
        //                            //echo "nombre archivo: $file2 : tipo archivo: " . filetype($dir.$file."/".$file2) . "\n";
        //                        }
        //                        closedir($dh1);
        //                    }
        //                }
        //            }
        //        }
        //        closedir($dh);
        //    }
        //}
//
//
        //exit(1);
//


        $tipos = ['Gebraucht' => "Usado", 'Oldtimer' => "Clásico", 'Neu' => "Nuevo"];
        $transmisiones = ['Automatik' => "Automático", 'Handschaltung' => "Manual", 'Halbautomatik' => 'Semiautomático'];
        $cuerpos = [
            'Limousine',
            'Van/Kleinbus',
            'Cabrio/Roadster',
            'SUV/Geländewagen',
            'Kombi',
            'Sonstiges',
            'Kleinwagen',
            'Sportwagen/Coupé',
            'Sonstige Moto',
            'Cabrio/Roadster',
            'Lieferwagen'
        ];


        ////Create Cuerpos DB
        //foreach($cuerpos as $cuer){
        //    $bodytype_webmobile = DB::table('bodytypes_webmobile24')->where('slug', Str::slug($cuer))->first();
        //    if ($bodytype_webmobile == null && $cuer != "") {
        //        DB::table('bodytypes_webmobile24')->insert([
        //            'name' =>  $cuer,
        //            'slug' => Str::slug($cuer)
        //        ]);
        //    }
        //}
        //exit(1);


        $combustibles = [
            'Diesel',
            'Elektro/ Benzin',
            'Benzin',
            'Elektro',
            'LPG',
            //..  existen: 'Elektro/ Diesel'? CNG? Hidrógeno?..
        ];

        ////Create fueltypes_webmobile24 DB
        //foreach($combustibles as $fuel){
        //    $fueltypes_webmobile24 = DB::table('fueltype_webmobile24')->where('slug', Str::slug($fuel))->first();
        //    if ($fueltypes_webmobile24 == null && $fuel != "") {
        //        DB::table('fueltype_webmobile24')->insert([
        //            'name' =>  $fuel,
        //            'slug' => Str::slug($fuel)
        //        ]);
        //    }
        //}
        //exit(1);



        //Get Attributes from db
        $attributes_db = DB::table('attributes')->get();

        //Open Dir webmobile24/ in root folder
        $dir = "webmobile24/";

        //Get Countrycode
        $country = DB::table('countries')->where('code', 'DE')->first();

        // Abre un directorio conocido, y procede a leer el contenido
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {

                    if ($file != "." && $file != "..") {
                        echo "nombre archivo: $file : tipo archivo: " . filetype($dir . $file) . "\n";
                        if (is_dir($dir . $file)) {
                            //Open Subdirs on webmobile24
                            if ($dh1 = opendir($dir . $file)) {
                                while (($file2 = readdir($dh1)) !== false) {
                                    //echo filetype($file2) . " \n";
                                    if (pathinfo($file2, PATHINFO_EXTENSION) == "csv") {
                                        //Get CSV file name
                                        echo "file csv: " . $dir . $file . "/" . $file2 . "\n";
                                        //Open the file.
                                        $fileHandle = fopen((string)$dir . $file . "/" . $file2, "r");

                                        $tipos_usados  = [];

                                        //Create User and Shop for User
                                        $user = DB::table('users')->where('email', $file . "@" . $file . ".com")->first();
                                        if (empty($user)) {
                                            //Create User
                                            DB::table('users')->insert([
                                                'name' => $file,
                                                'user_type' => 'seller',
                                                'email' => $file . "@" . $file . ".com",
                                                'password' => bcrypt("test123"),
                                                'email_verified_at' => Carbon::now()
                                            ]);
                                            $user = DB::table('users')->where('email', $file . "@" . $file . ".com")->first();
                                        }
                                        $seller = DB::table('sellers')->where('email', $file . "@" . $file . ".com")->first();
                                        if (empty($seller)) {
                                            //Create Shop
                                            //DB::table('shops')->insert([
                                            //    'email' => $file . "@" . $file . ".com",
                                            //    'name' => $file,
                                            //    'user_id' => $user->id,
                                            //]);

                                            $randomFloat = rand(0, 50) / 10;
                                            //Create Shop
                                            DB::table('sellers')->insert(
                                                [
                                                    'name' => $file,
                                                    'email' => $file . "@" . $file . ".com",
                                                    'user_id' => $user->id,
                                                    'slug' => Str::slug($file),
                                                    'professional' => 0,
                                                    'rating' => $randomFloat,
                                                    'seller_package_id' => 1,
                                                    'remaining_uploads' => 1000,
                                                    'remaining_digital_uploads' => 1000,
                                                    'verification_status' => 1,
                                                    'verification_info' => '[{"type":"text","label":"Name","value":"Mr. Seller"},{"type":"select","label":"Marital Status","value":"Married"},{"type":"multi_select","label":"Company","value":"[\"Company\"]"},{"type":"select","label":"Gender","value":"Male"},{"type":"file","label":"Image","value":"uploads\/verification_form\/CRWqFifcbKqibNzllBhEyUSkV6m1viknGXMEhtiW.png"}]',
                                                    'cash_on_delivery_status' => 1,
                                                    'admin_to_pay' => 78.40,
                                                    'country_code' => $country->id
                                                ]
                                            );

                                            //
                                            $seller = DB::table('sellers')->where('email', $file . "@" . $file . ".com")->first();
                                        }

                                        //Create Shop
                                        $shop = DB::table('shops')->where('email', $file . "@" . $file . ".com")->first();
                                        if (empty($shop)) {



                                            //Create Shop
                                            DB::table('shops')->insert([
                                                'name' => $file,
                                                'email' => $file . "@" . $file . ".com",
                                                'user_id' => $user->id,
                                                'slug' => Str::slug($file),
                                                'country_code' => $country->id,
                                                'user_id' => $user->id,
                                                'vat' => $seller->vat,
                                                'country' => $country->id
                                            ]);
                                            $shop = DB::table('shops')->where('email', $file . "@" . $file . ".com")->first();
                                        }

                                        $shop_address = DB::table('addresses')->where('user_id', $shop->id)->where('name', $file)->first();
                                        if (empty($shop_address)) {
                                            //Create Address
                                            DB::table('addresses')->insert([
                                                'user_id' => $shop->id,
                                                'address' => '',
                                                'postal_code' => '',
                                                'phone' => '',
                                                'set_default' => 0,
                                                'name' => (string)$file,
                                            ]);
                                            $shop_address = DB::table('addresses')->where('user_id', $shop->id)->where('name', $file)->first();
                                        }
                            
                                        //CountersSeller
                                        if (!in_array((string)$seller->email, $arraySellers)) {
                                            array_push($arraySellers, (string)$seller->email);
                                            $totalSellerCounter++;
                                        }


                                        //Loop through the CSV rows.
                                        while (($row = fgetcsv($fileHandle, 0, ";")) !== FALSE) {
                                            //Dump out the row for the sake of clarity.
                                            //var_dump($row);
                                            //exit(1);

                                            //Get Imagen Code
                                            $imagenes = $row[2];
                                            $imagen_code =  preg_split("/_/", $imagenes);
                                            $imagen_code = $imagen_code[0]; // Ejemplo: 146917784

                                            $externalId = $imagen_code;

                                            //Car Data Example
                                            //https://www.romoto.de/inserat/146917784

                                            $marca = utf8_encode($row[3]); //OK
                                            $modelo = utf8_encode($row[4]); //OK
                                            $submodelo = utf8_encode($row[5]); //OK
                                            $tipo_usado = $row[6]; //OK
                                            $transmision = $row[7]; //OK
                                            $cuerpo = $row[8]; //OK
                                            $color = $row[9];
                                            $combustible = $row[12]; //OK
                                            $year = $row[13]; //OK
                                            $kilometros = $row[14]; ///OK
                                            $price = $row[15];//OK
                                            $puertas = $row[16]; //OK
                                            $asientos = $row[17]; //OK
                                            $cilindrada = $row[18]; //OK
                                            $caballosKW = $row[19]; //OK
                                            // ..
                                            $descripcion = $row[58]; //OK
                                            //..
                                            $claseContaminante = $row[86]; //OK
                                            //..

                                            $check_color = null;
                                            //Get Color UTF8encoded
                                            $color_encode = utf8_encode($color);
                                            //Check color encoded not null
                                            if ($color_encode != "" && $color_encode != " " && !empty($color_encode)) {
                                                //Check Colors Webmobile
                                                $check_color = DB::table('colors_webmobile')->where('name', $color_encode)->first();
                                                if ($check_color == null) {
                                                    DB::table('colors_webmobile')->insert(array(
                                                        'name' => $color_encode,
                                                    ));
                                                }
                                            }
                                            //Get Colors from DB
                                            $colors = null;
                                            if($check_color != null){
                                                $colors = DB::table('colors')->where('id' , $check_color->color_id)->first();
                                            }

                                            //Fix Year (10.1999) to 1999
                                            $year_car = null;
                                            if ($year != null) {
                                                //$this->info($year);
                                                $year_array = preg_split("/\./", $year);
                                                if (count($year_array) >= 1) {
                                                    $year_car = $year_array[1];
                                                }
                                            }

                                            //Fix Descripcion Text
                                            $descripcion = str_replace("---------*", "\n", $descripcion);
                                            $descripcion = str_replace(" **", "", $descripcion);
                                            $descripcion = str_replace("\\\\*", "", $descripcion);
                                            $descripcion = str_replace("\\\\", "\n", $descripcion);
                                            $descripcion = str_replace("---------\\", "", $descripcion);
                                            $descripcion = str_replace("**\\\\", "\n", $descripcion);
                                            $descripcion = str_replace("**", "\n", $descripcion);
                                            $descripcion = str_replace("*", "", $descripcion);
                                            $descripcion = utf8_encode($descripcion);

                                            //echo $descripcion . "\n";
                                            //$this->info("Next description");

                                            //Fix tipo vehiculod esmostracion
                                            if(Str::slug($tipo_usado) == Str::slug("Vorf�hrfahrzeug")){
                                                $tipo_usado = "Vehículo de demostración";
                                            }

                                            //Get Tipo usado Value ( nuevo, usado, clásico)
                                            foreach ($tipos as $key => $value) {
                                                if ($tipo_usado == $key) {
                                                    $tipo_usado = $value;
                                                }
                                            }

                                            $attributes =  [];
                                            $attributes_values = [];


                                            //Fix Cuerpo name
                                            if (Str::slug($cuerpo) == Str::slug("SUV/Gelndewagen")) {
                                                $cuerpo = "SUV/Geländewagen";
                                            }
                                            //Fix Cuerpo name
                                            if (Str::slug($cuerpo) == Str::slug("Sportwagen/Coup")) {
                                                $cuerpo = "Sportwagen/Coupé";
                                            }

                                            //Get Bodsytype or Create
                                            $body_db = DB::table('bodytypes_webmobile24')->where('slug', Str::slug($cuerpo))->first();
                                            if ($body_db == null && $cuerpo != "") {
                                                DB::table('bodytypes_webmobile24')->insert([
                                                    'name' =>  $cuerpo,
                                                    'slug' => Str::slug($cuerpo)
                                                ]);
                                                $body_db =  DB::table('bodytypes_webmobile24')->orderBy('id', 'DESC')->first();
                                            }
                                            //Get Cuerpo name
                                            if (isset($body_db->bodytype_id)) {
                                                $body = DB::table('bodytypes')->where('id', $body_db->bodytype_id)->first();
                                                if (isset($body->name)) {
                                                    $cuerpo = $body->name;
                                                }
                                            }

                                            //Insert Cuerpo Attributes Values or Create
                                            //Get Attribute cuerpo
                                            $cuerpo_db = $attributes_db->where('name', 'Cuerpo')->first();
                                            //Get Attribute value Cuerpo
                                            $attr_value = DB::table('attribute_values')->where('value', $cuerpo)->first();
                                            if (!isset($attr_value)) {

                                                //Check Attribute Values Translations
                                                $attr_val_translate = DB::table('attribute_values_translations')->where('name', $cuerpo)->first();
                                                if(!isset($attr_val_translate) && $cuerpo_db->is_choice == 1){
                                                    DB::table('attribute_values')->insert([
                                                        'value' =>  $cuerpo,
                                                        'attribute_id' => $cuerpo_db->id,
                                                    ]);
                                                }
                                            }
                                            array_push($attributes, $cuerpo_db->id);
                                            array_push($attributes_values, array("attribute_id" =>  $cuerpo_db->id, "values" => [$cuerpo]));
                                            //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                            //array_push($attributes_values, array("attribute_id" =>  $cuerpo_db->id, "values" => [$attr_value->id]));
                                            $attr_value = null;



                                            //Insert Usado Attributes Values or Create
                                            //Get Attribute Usado
                                            $tipo_db = $attributes_db->where('name', 'Usado')->first();
                                            //Get Attribute value Usado
                                            $attr_value = DB::table('attribute_values')->where('value', $tipo_usado)->first();
                                            if (!isset($attr_value)) {

                                                //Check Attribute Values Translations
                                                $attr_val_translate = DB::table('attribute_values_translations')->where('name', $tipo_usado)->first();
                                                if(!isset($attr_val_translate) && $tipo_db->is_choice == 1){
                                                    DB::table('attribute_values')->insert([
                                                        'value' =>  $tipo_usado,
                                                        'attribute_id' => $tipo_db->id,
                                                    ]);
                                                }
                                            }

                                            array_push($attributes, $tipo_db->id);
                                            array_push($attributes_values, array("attribute_id" =>  $tipo_db->id, "values" => [$tipo_usado]));
                                            //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                            //array_push($attributes_values, array("attribute_id" => $tipo_db->id, "values" => [$attr_value->id]));
                                            $attr_value = null;

                                            //Combustible
                                            $combustible_usado = DB::table('fueltype_webmobile24')->where('slug', Str::slug($combustible))->first();
                                            if(empty($combustible_usado)){
                                                DB::table('fueltype_webmobile24')->insert([
                                                    'name' => $combustible,
                                                    'slug' => Str::slug($combustible)
                                                ]);

                                                $combustible_usado = DB::table('fueltype_webmobile24')->orderBy('id', 'DESC')->first();
                                            }

                                            $fueltype = DB::table('fueltype')->where('id', $combustible_usado->fueltype_id)->first();
                                            if(empty($fueltype)){
                                                continue;
                                            }

                                            //Insert Combustible Attributes Values or Create
                                            //Get Attribute Combustible
                                            $combustible_db = $attributes_db->where('name', 'Combustible')->first();
                                            //Get Attribute value Combustible
                                            $attr_value = DB::table('attribute_values')->where('value', $fueltype->name)->first();
                                            if (!isset($attr_value) && !empty($fueltype)) {

                                                //Check Attribute Values Translations
                                                $attr_val_translate = DB::table('attribute_values_translations')->where('name', $fueltype->name)->first();
                                                if(!isset($attr_val_translate) && $combustible_db->is_choice == 1){
                                                    DB::table('attribute_values')->insert([
                                                        'value' =>  $fueltype->name,
                                                        'attribute_id' => $combustible_db->id,
                                                    ]);
                                                }
                                            }
                                            array_push($attributes_values,array("attribute_id" =>  $combustible_db->id, "values" => [$fueltype->name]));
                                            array_push($attributes, $combustible_db->id);
                                            //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                            //array_push($attributes_values, array("attribute_id" =>  $combustible_db->id, "values" => [$attr_value->id]));
                                            $attr_value = null;


                                            if (!empty($cilindrada)) {
                                                //Insert Cilindrada Attributes Values or Create
                                                //Get Attribute Cilindrada
                                                $cilindrada_db = $attributes_db->where('name', 'Cilindrada')->first();
                                                //Get Attribute value Cilindrada
                                                $attr_value = DB::table('attribute_values')->where('attribute_id', $cilindrada_db->id)->where('value',  $cilindrada . " CC")->first();
                                                if (empty($attr_value)) {

                                                    //Check Attribute Values Translations
                                                    $attr_val_translate = DB::table('attribute_values_translations')->where('name', $cilindrada . " CC")->first();
                                                    if(!isset($attr_val_translate) && $cilindrada_db->is_choice == 1){
                                                        DB::table('attribute_values')->insert([
                                                            'value' =>  $cilindrada . " CC",
                                                            'attribute_id' => $cilindrada_db->id,
                                                        ]);
                                                    }
                                                }
                                                array_push($attributes_values, array("attribute_id" =>  $cilindrada_db->id, "values" => [$cilindrada . " CC"]));
                                                array_push($attributes, $cilindrada_db->id);
                                                //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                                //array_push($attributes_values, array("attribute_id" => $cilindrada_db->id, "values" => [$attr_value->id]));
                                                $attr_value = null;
                                            }



                                            if ($puertas > 0) {
                                                //Insert Puertas Attributes Values or Create
                                                //Get Attribute Puertas
                                                $puerta_db = $attributes_db->where('name', 'Puertas')->first();
                                                //Get Attribute value Puertas
                                                $attr_value = DB::table('attribute_values')->where('attribute_id', $puerta_db->id)->where('value', $puertas . " puertas")->first();
                                                if (!isset($attr_value)) {

                                                    //Check Attribute Values Translations
                                                    $attr_val_translate = DB::table('attribute_values_translations')->where('name', $puertas . " puertas")->first();
                                                    if(!isset($attr_val_translate) && $puerta_db->is_choice == 1){
                                                        DB::table('attribute_values')->insert([
                                                            'value' =>  $puertas . " puertas",
                                                            'attribute_id' => $puerta_db->id,
                                                        ]);
                                                    }
                                                }
                                                array_push($attributes, $puerta_db->id);
                                                array_push($attributes_values, array("attribute_id" =>  $puerta_db->id, "values" => [$puertas . " puertas"]));
                                                //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                                //array_push($attributes_values, array("attribute_id" =>  $puerta_db->id, "values" => [$attr_value->id]));
                                                $attr_value = null;
                                            }


                                            if ($asientos > 0) {
                                                //Insert Asientos Attributes Values or Create
                                                //Get Attribute Asientos
                                                $asiento_db = $attributes_db->where('name', 'Asientos')->first();
                                                //Get Attribute value Asientos
                                                $attr_value = DB::table('attribute_values')->where('value', $asientos . " asientos")->first();
                                                if (!isset($attr_value)) {

                                                    //Check Attribute Values Translations
                                                    $attr_val_translate = DB::table('attribute_values_translations')->where('name', $asientos . " asientos")->first();
                                                    if(!isset($attr_val_translate) && $asiento_db->is_choice == 1){
                                                        DB::table('attribute_values')->insert([
                                                            'value' =>  $asientos . " asientos",
                                                            'attribute_id' => $asiento_db->id,
                                                        ]);
                                                    }
                                                }
                                                array_push($attributes, $asiento_db->id);
                                                array_push($attributes_values, array("attribute_id" =>  $asiento_db->id, "values" => [$asientos . " asientos"]));
                                                //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                                //array_push($attributes_values, array("attribute_id" =>  $asiento_db->id, "values" => [$attr_value->id]));
                                                $attr_value = null;
                                            }



                                            if (isset($claseContaminante) && $claseContaminante != "") {
                                                //Insert Clase Contaminante Attributes Values or Create
                                                //Get Attribute Clase Contaminante
                                                $claseCo2_db = $attributes_db->where('name', 'Clase contaminante')->first();
                                                //Get Attribute value Clase Contaminante
                                                $attr_value = DB::table('attribute_values')->where('attribute_id', $claseCo2_db->id)->where('value', $claseContaminante)->first();
                                                if (empty($attr_value) &&  $claseContaminante != " " && $claseContaminante != null) {

                                                    //Check Attribute Values Translations
                                                    $attr_val_translate = DB::table('attribute_values_translations')->where('name', $claseContaminante)->first();
                                                    if(!isset($attr_val_translate) && $claseCo2_db->is_choice == 1){
                                                        DB::table('attribute_values')->insert([
                                                            'value' =>  $claseContaminante,
                                                            'attribute_id' => $claseCo2_db->id,
                                                        ]);
                                                    }
                                                }
                                                array_push($attributes, $claseCo2_db->id);
                                                array_push($attributes_values, array("attribute_id" =>  $claseCo2_db->id, "values" => [$claseContaminante]));
                                                //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                                //array_push($attributes_values, array("attribute_id" =>  $claseCo2_db->id, "values" => [$attr_value->id]));
                                                $attr_value = null;
                                            }


                                            if ($caballosKW > 0) {
                                                //Caballos KW
                                                $caballos = round($caballosKW * 1.35962161); // Cambiar de KW a CV
                                                //Get Attribute Caballos
                                                $caballos_db = $attributes_db->where('name', 'Caballos')->first();
                                                //Get Attribute value Caballos
                                                $attr_value = DB::table('attribute_values')->where('attribute_id', $caballos_db->id)->where('value', $caballos . " CV")->first();
                                                if (empty($attr_value)) {

                                                    //Check Attribute Values Translations
                                                    $attr_val_translate = DB::table('attribute_values_translations')->where('name', $caballos . " CV")->first();

                                                    if(!isset($attr_val_translate) && $caballos_db->is_choice == 1){
                                                        DB::table('attribute_values')->insert([
                                                            'value' =>  $caballos . " CV",
                                                            'attribute_id' => $caballos_db->id,
                                                        ]);
                                                    }
                                                }
                                                array_push($attributes, $caballos_db->id);
                                                array_push($attributes_values, array("attribute_id" =>  $caballos_db->id, "values" => [$caballos . " CV"]));
                                                //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                                //array_push($attributes_values, array("attribute_id" =>  $caballos_db->id, "values" => [$attr_value->id]));
                                                $attr_value = null;
                                            }


                                            //if (!empty($year)) {
                                            //    //Year
                                            //    $year_db = $attributes_db->where('name', 'Fecha de registro')->first();
                                            //    //Get Attribute value Año registro
                                            //    $attr_value = DB::table('attribute_values')->where('attribute_id', $year_db->id)->where('value', '=', $year)->first();
                                            //    if (!isset($attr_value)) {
                                            //        DB::table('attribute_values')->insert([
                                            //            'value' =>  $year,
                                            //            'attribute_id' => $year_db->id,
                                            //        ]);
                                            //    }
                                            //    array_push($attributes, $year_db->id);
                                            //    array_push($attributes_values, array("attribute_id" =>  $year_db->id, "values" => [$year]));
                                            //    $attr_value = null;
                                            //}


                                            //if (!empty($kilometros)) {
                                            //    //Year
                                            //    $km_db = $attributes_db->where('name', 'Kilómetros')->first();
                                            //    //Get Attribute value Año registro
                                            //    $attr_value = DB::table('attribute_values')->where('attribute_id', $km_db->id)->where('value', '=', $kilometros)->first();
                                            //    if (!isset($attr_value)) {
                                            //        DB::table('attribute_values')->insert([
                                            //            'value' =>  $kilometros,
                                            //            'attribute_id' => $km_db->id,
                                            //        ]);
                                            //    }
                                            //    array_push($attributes, $km_db->id);
                                            //    array_push($attributes_values, array("attribute_id" =>  $km_db->id, "values" => [$kilometros]));
                                            //    $attr_value = null;
                                            //}


                                            //Get Transmision Value (manual, automatico, semiautomatico)
                                            foreach ($transmisiones as $key => $value) {
                                                if ($transmision == $key) {
                                                    $transmision = $value;
                                                }
                                            }

                                            //Insert Transmision Attributes Values or Create
                                            //Get Attribute Transmision
                                            $transmision_db = $attributes_db->where('name', 'Transmisión')->first();
                                            //Get Attribute value Transmision
                                            $attr_value = DB::table('attribute_values')->where('value', $transmision)->first();
                                            if (!isset($attr_value)) {

                                                //Check Attribute Values Translations
                                                $attr_val_translate = DB::table('attribute_values_translations')->where('name', $transmision)->first();
                                                if(!isset($attr_val_translate) && $transmision_db->is_choice == 1){
                                                    DB::table('attribute_values')->insert([
                                                        'value' =>  $transmision,
                                                        'attribute_id' => $transmision_db->id,
                                                    ]);
                                                }
                                            }
                                            array_push($attributes, $transmision_db->id);
                                            array_push($attributes_values, array("attribute_id" =>  $transmision_db->id, "values" => [$transmision]));
                                            //Mod choice options => [{"attribute_id": 1, "values": [1]}]
                                            //array_push($attributes_values, array("attribute_id" =>  $transmision_db->id, "values" => [$attr_value->id]));
                                            $attr_value = null;




                                            //Fix Brand == VW (Volskwagen)
                                            if (Str::slug($marca) == "vw") {
                                                $marca = "Volkswagen";
                                                //$this->info(sprintf('Desc %s', $marca));
                                            }


                                            //Get Brand from DB
                                            $brand = DB::table('brands')->where('slug', '=', Str::slug($marca))->first();
                                            //Create new Brand if not exists
                                            if (isset($brand) == null) {
                                                DB::table('brands')->insert(
                                                    [
                                                        'name' => $marca,
                                                        'logo' => 1,
                                                        'top' => 1,
                                                        'slug' => Str::slug($marca),
                                                        'meta_title' => $marca
                                                    ]
                                                );
                                                $brand = DB::table('brands')
                                                    ->where('slug', '=', Str::slug($marca))
                                                    ->first();
                                            }



                                            $category = DB::table('categories')->where('level', 0)
                                                ->where('slug', Str::slug($marca))
                                                ->where('ads_type', 1)
                                                ->first();

                                            //Create new Category if not exists
                                            if (isset($category) == null) {
                                                DB::table('categories')->insert(
                                                    [
                                                        'name' => $marca,
                                                        'top' => 1,
                                                        'level' => 0,
                                                        'slug' => Str::slug($marca),
                                                        'meta_title' => $marca,
                                                        'ads_type' => 1,
                                                    ]
                                                );
                                                $category = DB::table('categories')->where('level', 0)
                                                    ->where('slug', '=', Str::slug($marca))
                                                    ->where('ads_type', 1)
                                                    ->first();

                                                //Insert into categories_portalclub
                                                DB::table('categories_webmobile24')->insert(
                                                    [
                                                        'category_id' => $category->id,
                                                        'name'  => $category->name,
                                                        'slug'  => $category->slug,
                                                        'meta_title'  => $category->meta_title
                                                    ]
                                                );
                                                // ???????????????????
                                                ////Insert Marca Attributes Values or Create
                                                //$tipo_db = $attributes_db->where('name', 'Fabricante')->first();
                                                ////Get Attribute value Usado
                                                //$attr_value = DB::table('attribute_values')->where('value', $marca)->first();
                                                //if (!isset($attr_value)) {
                                                //    DB::table('attribute_values')->insert([
                                                //        'value' =>  $marca,
                                                //        'attribute_id' => $tipo_db->id,
                                                //    ]);
                                                //}

                                                //array_push($attributes, $tipo_db->id);
                                                //array_push($attributes_values, array("attribute_id" =>  $tipo_db->id, "values" => [$tipo_usado]));
                                                $attr_value = null;
                                            }

                                            $son_category = DB::table('categories')->where('level', 1)
                                                ->where('slug', Str::slug($marca . " " . $modelo))
                                                ->where('ads_type', 1)
                                                ->first();
                                            //Create new Category if not exists
                                            if (isset($son_category) == null &&  $modelo != "") {
                                                DB::table('categories')->insert(
                                                    [
                                                        'name' => $marca . " " . $modelo,
                                                        'parent_id' => $category->id,
                                                        'level' => 1,
                                                        'top' => 1,
                                                        'slug' => Str::slug($marca . " " . $modelo),
                                                        'meta_title' => $marca . " " . $modelo,
                                                        'ads_type' => 1,
                                                        //'pc_model_id' => $ad->model->model['id']
                                                    ]
                                                );
                                                $son_category = DB::table('categories')->where('level', 1)
                                                    ->where('slug', '=', Str::slug($marca . " " . $modelo))
                                                    ->where('ads_type', 1)
                                                    ->first();
                                                //Insert into categories_portalclub
                                                DB::table('categories_webmobile24')->insert(
                                                    [
                                                        'category_id' => $son_category->id,
                                                        'name'  => $son_category->name,
                                                        'slug'  => $son_category->slug,
                                                        'meta_title'  => $son_category->meta_title
                                                    ]
                                                );
                                                // ???????????????????
                                                ////Insert Marca Attributes Values or Create
                                                //$tipo_db = $attributes_db->where('name', 'Modelo')->first();
                                                ////Get Attribute value Usado
                                                //$attr_value = DB::table('attribute_values')->where('value', $marca . " " . $modelo)->first();
                                                //if (!isset($attr_value)) {
                                                //    DB::table('attribute_values')->insert([
                                                //        'value' =>  $marca . " " . $modelo,
                                                //        'attribute_id' => $tipo_db->id,
                                                //    ]);
                                                //}

                                                //array_push($attributes, $tipo_db->id);
                                                //array_push($attributes_values, array("attribute_id" =>  $tipo_db->id, "values" => [$tipo_usado]));
                                                $attr_value = null;
                                            }
                                            $grandson_category =  DB::table('categories')->where('level', 2)
                                                ->where('slug', Str::slug($modelo . " " . $submodelo))
                                                ->where('ads_type', 1)
                                                ->first();

                                            //Create new Category if not exists
                                            if (isset($grandson_category) == null && !empty($submodelo)) {
                                                // Create brand
                                                DB::table('categories')->insert(
                                                    [
                                                        'name' =>  $modelo . " " . $submodelo,
                                                        'parent_id' => isset($son_category->id) ? $son_category->id : $category->id,
                                                        'level' => isset($son_category) ? 2 : 1,
                                                        'top' => 1,
                                                        'slug' => Str::slug($modelo . " " . $submodelo),
                                                        'meta_title' => $modelo . " " . $submodelo,
                                                        'ads_type' => 1,
                                                    ]
                                                );
                                                $grandson_category = DB::table('categories')
                                                    ->where('slug', '=', Str::slug($modelo . " " . $submodelo))
                                                    ->where('ads_type', 1)
                                                    ->first();

                                                //Insert into categories_portalclub
                                                DB::table('categories_webmobile24')->insert(
                                                    [
                                                        'category_id' => $grandson_category->id,
                                                        'name'  => $grandson_category->name,
                                                        'slug'  => $grandson_category->slug,
                                                        'meta_title'  => $grandson_category->meta_title
                                                    ]
                                                );
                                                // ???????????????????
                                                //Insert Version Attributes Values or Create
                                                //$tipo_db = $attributes_db->where('name', 'Version')->first();
                                                ////Get Attribute value Usado
                                                //$attr_value = DB::table('attribute_values')->where('value', $modelo . " " . $submodelo)->first();
                                                //if (!isset($attr_value)) {
                                                //    DB::table('attribute_values')->insert([
                                                //        'value' =>  $modelo . " " . $submodelo,
                                                //        'attribute_id' => $tipo_db->id,
                                                //    ]);
                                                //}
                                                ////array_push($attributes, $tipo_db->id);
                                                ////array_push($attributes_values, array("attribute_id" =>  $tipo_db->id, "values" => [$tipo_usado]));
                                                //$attr_value = null;
                                            }





                                            array_push($combustibles, $combustible);
                                            array_push($cuerpos, $cuerpo);


                                            //FILL PRODUCT

                                            //Set Product Category
                                            $product_category = isset($grandson_category) ? $grandson_category : $son_category;

                                            //Get Product Slug
                                            $product_slug = Str::slug((string) $marca .
                                                " " . $modelo . " " . $submodelo . " " . $user->id . "-" . $shop->id);

                                            //Check Exists product with slug or Create
                                            $prod = DB::table('products')->where('slug', $product_slug)->first();
                                            if (empty($prod)) {
                                                //Create Product - SKIP desciption Source: PortalClub. " .
                                                DB::table('products')->insert(
                                                    [
                                                        'user_id' => $user->id,
                                                        'shop_id' => $shop->id,
                                                        'name' => $marca . " " . $modelo . " " . $submodelo,
                                                        'added_by' => "seller",
                                                        'category_id' => $product_category->id,
                                                        'slug' => $product_slug,
                                                        //'photos' => "[1,2,3,4]",
                                                        'brand_id' => $brand->id,
                                                        'description'  => $descripcion,
                                                        'attributes' => json_encode($attributes),
                                                        'choice_options' => json_encode($attributes_values),
                                                        'meta_description' => (string) $descripcion,
                                                        'unit_price' => $price ? $price : 0,
                                                        'purchase_price' => $price ? $price : 0,
                                                        'unit' => $cuerpo,
                                                        'meta_title' => (string) $marca . " " . $modelo . " " . $submodelo,
                                                        'year' => $year_car,
                                                        'km' => $kilometros,
                                                        'country' => $country->id,
                                                        'source' => "webmobile",
                                                        'type_adds' => 1,
                                                        'external_id' => $externalId,
                                                        'colors' => $colors != null ? "[".$colors->id."]": "[]",
                                                        'colors_int' => "[]",
                                                        'address_id' => $shop_address->id
                                                        //'video_provider' => (string) $ad->youtube_code ? 'youtube' : null,
                                                        //'video_link' => (string) $ad->youtube_code ? (string)'https://www.youtube.com/watch?v=' . (string) $ad->youtube_code : null
                                                    ]
                                                );

                                                //Log created
                                                $this->info(sprintf("Ad Created: %s", ($marca . " " . $modelo . " " . $submodelo)));

                                                //////////////////////////////////////////////////////////////
                                                //                  PROCESAR ARCHIVOS IMAGENES              //
                                                //////////////////////////////////////////////////////////////

                                                //Open dir empresa
                                                $dir3 = "webmobile24/".$file."/";
                                                $last_product = DB::table('products')->orderBy('id', 'DESC')->first();
                                                $images_array_ids = [];
                                                // Abre un directorio conocido, y procede a leer el contenido
                                                if (is_dir($dir3)) {
                                                    if ($dh3 = opendir($dir3)) {
                                                        while (($file3 = readdir($dh3)) !== false) {

                                                            if ($file3 != "." && $file3 != "..") {
                                                                //echo "nombre archivo: $file : tipo archivo: " . filetype($dir3 . $file) . "\n";

                                                                //Get Imagen Code
                                                                $imagenes = $file3;
                                                                $imagen_code1 =  preg_split("/_/", $imagenes);
                                                                $imagen_code1 = $imagen_code1[0]; //12312345

                                                                if($imagen_code == $imagen_code1){

                                                                    // Copy From  webmobile24/SELLER/FILE.jpg to public/uploads/all/
                                                                    $rootfolder = $_SERVER['DOCUMENT_ROOT'];
                                                                    $folderPath = $rootfolder . "uploads/webmobile24/all/"; // desired directory

                                                                    $currentfile = "webmobile24/".$file."/" . $file3;
                                                                    $destination = $folderPath . $file3;

                                                                    try{
                                                                        if (!file_exists($destination)) {
                                                                            copy($currentfile, $destination); // copy $currentfile into public/uploads/all/
                                                                            $this->info("copy image into: " . $destination);
                                                                        }
                                                                    }catch(Exception $exception){
                                                                        //$this->info("Error:" . $e);
                                                                        //$this->info("Error:" . $e);
                                                                        Log::channel('imports')->error(
                                                                            sprintf(
                                                                                '==> Webmobile:: Failed to load image  with error: %s...;',
                                                                                $exception->getMessage(),
                                                                            )
                                                                        );
                                                                        //continue;
                                                                    }

                                                                    //var_dump($imagen_code);
                                                                    //var_dump($file);
                                                                    //Original filename => $file3
                                                                    //Get filename
                                                                    $file_all =  preg_split("/\./", $file3);
                                                                    $filename = $file_all[0];

                                                                    //Get extension
                                                                    $extension = $file_all[1];

                                                                    //Insert into Uploads
                                                                    DB::table('uploads')->insert([
                                                                        'file_original_name' => $filename,
                                                                        'file_name' => "uploads/webmobile24/all/". $file3,
                                                                        'shop_name' => $seller->name,
                                                                        'extension' => $extension,
                                                                        'product_id' => $last_product->id,
                                                                        'type' => 'image'
                                                                    ]);
                                                                    //Get last Upload
                                                                    $last_upload = DB::table('uploads')->orderBy('id', 'DESC')->first();
                                                                    //Create Array uploads para cada producto
                                                                    array_push($images_array_ids, $last_upload->id);
                                                                }
                                                            }
                                                        }

                                                    }
                                                    closedir($dh3);
                                                }

                                                //Remove [] on string images_array_ids ( products-> photos )
                                                $string_ids = json_encode($images_array_ids);
                                                $string_ids = str_replace("[", "", $string_ids);
                                                $string_ids = str_replace("]", "", $string_ids);
                                                $string_ids = str_replace('"', "", $string_ids);


                                                //Update products if photos
                                                if ($images_array_ids != null) {
                                                    //Update product set photos array images ids
                                                    DB::table('products')->where('id', $last_product->id)->update(array(
                                                        'photos' => $string_ids,
                                                        'thumbnail_img' => $images_array_ids[0]
                                                    ));
                                                    $this->info(sprintf('==> UPDATE product_id %s, set photos %s ', $last_product->id, json_encode($images_array_ids)));
                                                }

                                                //Fill Product Attribute Values
                                                foreach(json_decode($last_product->choice_options) as $option){
                                                    $attribute_id = null;
                                                    $attribute_value = null;
                                                    //Parse choice_options
                                                    foreach($option as $key => $value){
                                                        //Check key attribute_id
                                                        if($key == "attribute_id"){
                                                            //Set Attribute id
                                                            $attribute_id = $value;
                                                            //$this->info("key: " . $value);
                                                        }
                                                        //Check key values
                                                        if($key == "values"){
                                                            //Parse Value
                                                            $valor = json_encode($value);
                                                            $valor = str_replace('"', "", $valor);
                                                            $valor = str_replace("[", "", $valor);
                                                            $valor = str_replace("]", "", $valor);

                                                            //Set Attribute Valor
                                                            $attribute_value = $valor;
                                                            //$this->info("value: ". $valor);
                                                        }
                                                    }

                                                    //$this->info("product_id: ". $iprod->id." attribute_id: " . $attribute_id. " value: ". $attribute_value);
                                                    //Check product extist
                                                    $check_pav = DB::table('product_attribute_values')->where('product_id', $last_product->id)->where('attribute_id', $attribute_id)->first();

                                                    if(empty($check_pav)){
                                                        //Insert into product_attribute_values
                                                        DB::table('product_attribute_values')->insert([
                                                            'product_id' => $last_product->id,
                                                            'attribute_id' => $attribute_id,
                                                            'value_choice' => $attribute_value
                                                        ]);
                                                    }
                                                }

                                                $totalAdsCounter++;
                                            }else{

                                                $this->info(sprintf("Ad Repetido: %s", $externalId));
                                                //Mod update_at
                                                DB::table('products')->where('id', $prod->id)->update(array(
                                                    'updated_at' => Carbon::now()
                                                ));

                                                //Check Empty Colors []
                                                if ($prod->colors == "[]" && $colors != null) {
                                                    DB::table('products')->where('id', $prod->id)->update(array(
                                                        'colors' => "[" . $colors->id . "]",
                                                    ));
                                                    $this->info(sprintf("Updated product id %s colors set exterior_color", $prod->id));
                                                }

                                                //Fill Product Attribute Values
                                                foreach (json_decode($prod->choice_options) as $option) {
                                                    $attribute_id = null;
                                                    $attribute_value = null;
                                                    //Parse choice_options
                                                    foreach ($option as $key => $value) {
                                                        //Check key attribute_id
                                                        if ($key == "attribute_id") {
                                                            //Set Attribute id
                                                            $attribute_id = $value;
                                                            //$this->info("key: " . $value);
                                                        }
                                                        //Check key values
                                                        if ($key == "values") {
                                                            //Parse Value
                                                            $valor = json_encode($value);
                                                            $valor = str_replace('"', "", $valor);
                                                            $valor = str_replace("[", "", $valor);
                                                            $valor = str_replace("]", "", $valor);

                                                            //Set Attribute Valor
                                                            $attribute_value = $valor;
                                                            //$this->info("value: ". $valor);
                                                        }
                                                    }

                                                    //$this->info("product_id: ". $iprod->id." attribute_id: " . $attribute_id. " value: ". $attribute_value);
                                                    //Check product extist
                                                    $check_pav = DB::table('product_attribute_values')->where('product_id', $prod->id)->where('attribute_id', $attribute_id)->first();

                                                    if (empty($check_pav)) {
                                                        //Insert into product_attribute_values
                                                        DB::table('product_attribute_values')->insert([
                                                            'product_id' => $prod->id,
                                                            'attribute_id' => $attribute_id,
                                                            'value_choice' => $attribute_value
                                                        ]);
                                                    }
                                                }

                                                $totalAdsRepetidos++;
                                            }
                                        }
                                        fclose($fileHandle);
                                    }
                                    //echo "nombre archivo: $file2 : tipo archivo: " . filetype($dir.$file . $file2) . "\n";
                                }
                                closedir($dh1);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }

        //Log
        Log::channel('imports')->info(
            sprintf(
                '==> Webmobile:: Ended import at: %s ,
                Total imported: %s
                Total Sellers: %s
                Total Ads Repetidos: %s',
                Carbon::now(),
                $totalAdsCounter,
                $totalSellerCounter,
                $totalAdsRepetidos
            )
        );

        exit(1);



        /*
        // Process Unique export file
        //Open the file.
        $fileHandle = fopen("export.csv", "r");

        $tipos_usados  = [];
        //Loop through the CSV rows.
        while (($row = fgetcsv($fileHandle, 0, ";")) !== FALSE) {
            //Dump out the row for the sake of clarity.

            var_dump($row);
            $this->info("asdsa");
            continue;
            //exit(1);
            $marca = utf8_encode($row[3]); //OK
            $modelo = utf8_encode($row[4]); //OK
            $submodelo = utf8_encode($row[5]); //OK
            $tipo_usado = $row[6]; //OK
            $transmision = $row[7]; //OK
            $cuerpo = $row[8]; //OK
            $color = $row[9];
            $combustible = $row[12]; //OK
            $year = $row[13]; //OK
            $kilometros = $row[15];
            $puertas = $row[16]; //OK
            $asientos = $row[17]; //OK
            $cilindrada = $row[18]; //OK
            $caballosKW = $row[19]; //OK
            // ..
            $descripcion = $row[58]; //OK
            //..
            $claseContaminante = $row[86]; //OK
            //..
            $price = $row[158];

            //Fix Descripcion Text
            $descripcion = str_replace("---------*", "\n", $descripcion);
            $descripcion =str_replace(" **", "", $descripcion);
            $descripcion =str_replace("\\\\*", "", $descripcion);
            $descripcion =str_replace("\\\\", "\n", $descripcion);
            $descripcion =str_replace("---------\\", "", $descripcion);
            $descripcion =str_replace("**\\\\", "\n", $descripcion);
            $descripcion =str_replace("**", "\n", $descripcion);
            $descripcion =str_replace("*", "", $descripcion);
            $descripcion = utf8_encode($descripcion);

            //echo $descripcion . "\n";
            //$this->info("Next description");

            //Get Tipo usado Value ( nuevo, usado, clásico)
            foreach ($tipos as $key => $value) {
                if ($tipo_usado == $key) {
                    $tipo_usado = $value;
                }
            }

            $attributes =  [];
            $attributes_values = [];


            //Fix Cuerpo name
            if (Str::slug($cuerpo) == Str::slug("SUV/Gelndewagen")) {
                $cuerpo = "SUV/Geländewagen";
            }
            //Fix Cuerpo name
            if (Str::slug($cuerpo) == Str::slug("Sportwagen/Coup")) {
                $cuerpo = "Sportwagen/Coupé";
            }

            //Get Bodsytype or Create
            $body_db = DB::table('bodytypes_webmobile24')->where('slug', Str::slug($cuerpo))->first();
            if ($body_db == null && $cuerpo != "") {
                DB::table('bodytypes_webmobile24')->insert([
                    'name' =>  $cuerpo,
                    'slug' => Str::slug($cuerpo)
                ]);
                $body_db =  DB::table('bodytypes_webmobile24')->orderBy('id', 'DESC')->first();
            }
            //Get Cuerpo name
            if (isset($body_db->bodytype_id)) {
                $body = DB::table('bodytypes')->where('id', $body_db->bodytype_id)->first();
                if (isset($body->name)) {
                    $cuerpo = $body->name;
                }
            }

            //Insert Cuerpo Attributes Values or Create
            $cuerpo_db = $attributes_db->where('name', 'Cuerpo')->first();
            //Get Attribute value Cuerpo
            $attr_value = DB::table('attribute_values')->where('value', $cuerpo)->first();
            if (!isset($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $cuerpo,
                    'attribute_id' => $cuerpo_db->id,
                ]);
            }
            array_push($attributes, $cuerpo_db->id);
            array_push($attributes_values, array("attribute_id" =>  $cuerpo_db->id, "values" => [$cuerpo]));
            $attr_value = null;



            //Insert Usado Attributes Values or Create
            $tipo_db = $attributes_db->where('name', 'Usado')->first();
            //Get Attribute value Usado
            $attr_value = DB::table('attribute_values')->where('value', $tipo_usado)->first();
            if (!isset($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $tipo_usado,
                    'attribute_id' => $tipo_db->id,
                ]);
            }

            array_push($attributes, $tipo_db->id);
            array_push($attributes_values, array("attribute_id" =>  $tipo_db->id, "values" => [$tipo_usado]));
            $attr_value = null;

            //Combustible
            $combustible_usado = DB::table('fueltype_webmobile24')->where('slug', Str::slug($combustible))->first();
            $fueltype = DB::table('fueltype')->where('id', $combustible_usado->fueltype_id)->first();

            //Insert Combustible Attributes Values or Create
            $combustible_db = $attributes_db->where('name', 'Combustible')->first();
            //Get Attribute value Combustible
            $attr_value = DB::table('attribute_values')->where('value', $fueltype->name)->first();
            if (!isset($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $fueltype->name,
                    'attribute_id' => $combustible_db->id,
                ]);
            }
            array_push($attributes_values, array("attribute_id" =>  $combustible_db->id, "values" => [$combustible]));
            array_push($attributes, $combustible_db->id);
            $attr_value = null;


            //Cilindrada
            //Insert Cilindrada Attributes Values or Create
            $cilindrada_db = $attributes_db->where('name', 'Cilindrada')->first();
            //Get Attribute value Cilindrada
            $attr_value = DB::table('attribute_values')->where('attribute_id', $cilindrada_db->id)->where('value',  $cilindrada . " CC")->first();
            if (empty($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $cilindrada . " CC",
                    'attribute_id' => $cilindrada_db->id,
                ]);
            }
            array_push($attributes_values, array("attribute_id" =>  $cilindrada_db->id, "values" => [$cilindrada . " CC"]));
            array_push($attributes, $cilindrada_db->id);
            $attr_value = null;

            //Puertas
            //Insert Puertas Attributes Values or Create
            $puerta_db = $attributes_db->where('name', 'Puertas')->first();
            //Get Attribute value Puertas
            $attr_value = DB::table('attribute_values')->where('attribute_id', $puerta_db->id)->where('value', $puertas . " puertas")->first();
            if (!isset($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $puertas . " puertas",
                    'attribute_id' => $puerta_db->id,
                ]);
            }
            array_push($attributes, $puerta_db->id);
            array_push($attributes_values, array("attribute_id" =>  $puerta_db->id, "values" => [$puertas . " puertas"]));
            $attr_value = null;


            //Insert Asientos Attributes Values or Create
            $asiento_db = $attributes_db->where('name', 'Asientos')->first();
            //Get Attribute value Asientos
            $attr_value = DB::table('attribute_values')->where('value', $asientos . " asientos")->first();
            if (!isset($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $asientos . " asientos",
                    'attribute_id' => $asiento_db->id,
                ]);
            }
            array_push($attributes, $asiento_db->id);
            array_push($attributes_values, array("attribute_id" =>  $asiento_db->id, "values" => [$asientos . " asientos"]));
            $attr_value = null;


            //Clase Contaminante
            //Insert Clase Contaminante Attributes Values or Create
            $claseCo2_db = $attributes_db->where('name', 'Clase contaminante')->first();
            //Get Attribute value Clase Contaminante
            $attr_value = DB::table('attribute_values')->where('attribute_id', $claseCo2_db->id)->where('value', $claseContaminante)->first();
            if (empty($attr_value) &&  $claseContaminante != " " && $claseContaminante != null) {
                DB::table('attribute_values')->insert([
                    'value' =>  $claseContaminante,
                    'attribute_id' => $claseCo2_db->id,
                ]);
            }
            array_push($attributes, $claseCo2_db->id);
            array_push($attributes_values, array("attribute_id" =>  $claseCo2_db->id, "values" => [$claseContaminante]));
            $attr_value = null;

            //Caballos KW
            $caballos = round($caballosKW * 1.35962161); // Cambiar de KW a CV
            $caballos_db = $attributes_db->where('name', 'Caballos')->first();
            //Get Attribute value Caballos
            $attr_value = DB::table('attribute_values')->where('attribute_id', $caballos_db->id)->where('value', $caballos . " CV")->first();
            if (empty($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $caballos . " CV",
                    'attribute_id' => $caballos_db->id,
                ]);
            }
            array_push($attributes, $caballos_db->id);
            array_push($attributes_values, array("attribute_id" =>  $caballos_db->id, "values" => [$caballos . " CV"]));
            $attr_value = null;

            //Year
            $year_db = $attributes_db->where('name', 'Fecha de registro')->first();
            //Get Attribute value Año registro
            $attr_value = DB::table('attribute_values')->where('attribute_id', $year_db->id)->where('value', '=', $year)->first();
            if (!isset($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $year,
                    'attribute_id' => $year_db->id,
                ]);
            }
            array_push($attributes, $year_db->id);
            array_push($attributes_values, array("attribute_id" =>  $year_db->id, "values" => [$year]));
            $attr_value = null;



            //Get Transmision Value (manual, automatico, semiautomatico)
            foreach ($transmisiones as $key => $value) {
                if ($transmision == $key) {
                    $transmision = $value;
                }
            }

            //Insert Transmision Attributes Values or Create
            $transmision_db = $attributes_db->where('name', 'Transmisión')->first();
            //Get Attribute value Transmision
            $attr_value = DB::table('attribute_values')->where('value', $transmision)->first();
            if (!isset($attr_value)) {
                DB::table('attribute_values')->insert([
                    'value' =>  $transmision,
                    'attribute_id' => $transmision_db->id,
                ]);
            }
            array_push($attributes, $transmision_db->id);
            array_push($attributes_values, array("attribute_id" =>  $transmision_db->id, "values" => [$transmision]));
            $attr_value = null;




            //Fix Brand == VW (Volskwagen)
            if (Str::slug($marca) == "vw") {
                $marca = "Volkswagen";
                //$this->info(sprintf('Desc %s', $marca));
            }


            //Get Brand from DB
            $brand = DB::table('brands')->where('slug', '=', Str::slug($marca))->first();
            //Create new Brand if not exists
            if (isset($brand) == null) {
                DB::table('brands')->insert(
                    [
                        'name' => $marca,
                        'logo' => 1,
                        'top' => 1,
                        'slug' => Str::slug($marca),
                        'meta_title' => $marca
                    ]
                );
                $brand = DB::table('brands')
                    ->where('slug', '=', Str::slug($marca))
                    ->first();
            }



            $category = DB::table('categories')->where('level', 0)->where('slug', Str::slug($marca))->first();

            //Create new Category if not exists
            if (isset($category) == null) {
                DB::table('categories')->insert(
                    [
                        'name' => $marca,
                        'top' => 1,
                        'level' => 0,
                        'slug' => Str::slug($marca),
                        'meta_title' => $marca,
                    ]
                );
                $category = DB::table('categories')
                    ->where('slug', '=', Str::slug($marca))
                    ->first();

                //Insert into categories_portalclub
                DB::table('categories_webmobile24')->insert(
                    [
                        'category_id' => $category->id,
                        'name'  => $category->name,
                        'slug'  => $category->slug,
                        'meta_title'  => $category->meta_title
                    ]
                );
            }

            $son_category = DB::table('categories')->where('level', 1)->where('slug', Str::slug($marca . " " . $modelo))->first();
            //Create new Category if not exists
            if (isset($son_category) == null &&  $modelo != "") {
                DB::table('categories')->insert(
                    [
                        'name' => $marca . " " . $modelo,
                        'parent_id' => $category->id,
                        'level' => 1,
                        'top' => 1,
                        'slug' => Str::slug($marca . " " . $modelo),
                        'meta_title' => $marca . " " . $modelo,
                        //'pc_model_id' => $ad->model->model['id']
                    ]
                );
                $son_category = DB::table('categories')
                    ->where('slug', '=', Str::slug($marca . " " . $modelo))
                    ->first();
                //Insert into categories_portalclub
                DB::table('categories_webmobile24')->insert(
                    [
                        'category_id' => $son_category->id,
                        'name'  => $son_category->name,
                        'slug'  => $son_category->slug,
                        'meta_title'  => $son_category->meta_title
                    ]
                );
            }
            $grandson_category =  DB::table('categories')->where('level', 2)->where('slug', Str::slug($modelo . " " . $submodelo))->first();

            //Create new Category if not exists
            if (isset($grandson_category) == null && !empty($submodelo)) {
                // Create brand
                DB::table('categories')->insert(
                    [
                        'name' =>  $modelo . " " . $submodelo,
                        'parent_id' => isset($son_category->id) ? $son_category->id : $category->id,
                        'level' => isset($son_category) ? 2 : 1,
                        'top' => 1,
                        'slug' => Str::slug($modelo . " " . $submodelo),
                        'meta_title' => $modelo . " " . $submodelo,
                    ]
                );
                $grandson_category = DB::table('categories')
                    ->where('slug', '=', Str::slug($modelo . " " . $submodelo))
                    ->first();

                //Insert into categories_portalclub
                DB::table('categories_webmobile24')->insert(
                    [
                        'category_id' => $grandson_category->id,
                        'name'  => $grandson_category->name,
                        'slug'  => $grandson_category->slug,
                        'meta_title'  => $grandson_category->meta_title
                    ]
                );
            }





            array_push($combustibles, $combustible);
            array_push($cuerpos, $cuerpo);


            //FILL PRODUCT

            //Set Product Category
            $product_category = isset($grandson_category) ? $grandson_category : $son_category;

            //Get Product Slug
            $product_slug = Str::slug((string) $marca .
                " " . $modelo . " " . $submodelo . " " . $externalId ."-". $sellerExternalId);

            //Check Exists product with slug or Create
            $prod = DB::table('products')->where('slug', $product_slug)->first();
            if (empty($prod)) {
                //Create Product - SKIP desciption Source: PortalClub. " .
                DB::table('products')->insert(
                    [
                        'user_id' => '-1',
                        'shop_id' => '-1',
                        'name' =>$marca . " " . $modelo . " " . $submodelo,
                        'category_id' => $product_category->id,
                        'slug' => $product_slug,
                        'photos' => "[1,2,3,4]",
                        'brand_id' => $brand->id,
                        'description'  => $descripcion,
                        'attributes' => json_encode($attributes),
                        'choice_options' => json_encode($attributes_values),
                        'meta_description' => (string) $descripcion,
                        'unit_price' => $price,
                        'purchase_price' => $price,
                        'unit' => $cuerpo,
                        'meta_title' => (string) $marca . " " . $modelo . " " . $submodelo,
                        //'video_provider' => (string) $ad->youtube_code ? 'youtube' : null,
                        //'video_link' => (string) $ad->youtube_code ? (string)'https://www.youtube.com/watch?v=' . (string) $ad->youtube_code : null
                    ]
                );

                //Log created
                $this->info(sprintf("Ad Created: %s", ($marca . " " . $modelo . " " . $submodelo)));

                //TODO:: Create Product PortalClub Images
                //$product = DB::table('products')->orderBy('id', 'DESC')->first();

                //DB::table('products_webmobile_images')->insert([
                //    'product_id' => $product->id,
                //    //'images' => $imagenes
                //]);
            }

        }
        fclose($fileHandle);

        $new_genres = array_unique($combustibles);
        //Log Values

        exit(1);
        */

        //foreach ($new_genres as $genre) {
        //    $this->info(sprintf("New genre detected: %s ", $genre));
        //}
        //$this->info(sprintf("New genre detected: %s ",  json_encode($attributes_values)));

    }
}
