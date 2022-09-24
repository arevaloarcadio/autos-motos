<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TruckAd\BulkDestroyTruckAd;
use App\Http\Requests\Admin\TruckAd\DestroyTruckAd;
use App\Http\Requests\Admin\TruckAd\IndexTruckAd;
use App\Http\Requests\Admin\TruckAd\StoreTruckAd;
use App\Http\Requests\Admin\TruckAd\UpdateTruckAd;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Ad,TruckAd,DealerShowRoom,AdSubCharacteristic,AdImage,VehicleCategory};
use Illuminate\Support\Facades\Redis as Redis;

class TruckAdsController extends Controller
{   
    use ApiController;

    /**
     * Display a listing of the resource.
     *
     * @param IndexTruckAd $request
     * @return array|Factory|View
     */
    public function index(IndexTruckAd $request)
    {

        if(Redis::exists('truck_ads')) {
            $promoted = json_decode(Redis::get('truck_ads'));

        }else{
            $promoted_simple_ads = TruckAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)')->inRandomOrder()->limit(25);

            foreach (TruckAd::getRelationships() as $key => $value) {
            $promoted_simple_ads->with($key);
            }
            $promoted = $promoted_simple_ads->get()->toArray();
            Redis::set('truck_ads',json_encode($promoted));
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TruckAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'construction_height_mm', 'lifting_height_mm', 'lifting_capacity_kg_m', 'permanent_total_weight_kg', 'allowed_pulling_weight_kg', 'payload_kg', 'max_weight_allowed_kg', 'empty_weight_kg', 'loading_space_length_mm', 'loading_space_width_mm', 'loading_space_height_mm', 'loading_volume_m3', 'load_capacity_kg', 'operating_weight_kg', 'operating_hours', 'axes', 'wheel_formula', 'hydraulic_system', 'seats', 'mileage', 'power_kw', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'interior_color', 'exterior_color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'
            ,'additional_vehicle_info','doors'],

            // set columns to searchIn
            ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'wheel_formula', 'hydraulic_system', 'emission_class', 'condition', 'interior_color', 'exterior_color', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','additional_vehicle_info','doors'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'construction_height_mm', 'lifting_height_mm', 'lifting_capacity_kg_m', 'permanent_total_weight_kg', 'allowed_pulling_weight_kg', 'payload_kg', 'max_weight_allowed_kg', 'empty_weight_kg', 'loading_space_length_mm', 'loading_space_width_mm', 'loading_space_height_mm', 'loading_volume_m3', 'load_capacity_kg', 'operating_weight_kg', 'operating_hours', 'axes', 'wheel_formula', 'hydraulic_system', 'seats', 'mileage', 'power_kw', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'interior_color', 'exterior_color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','additional_vehicle_info','doors'];
                
                
                    if ($request->filters) {
                        foreach ($columns as $column) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }
                $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');
                
                foreach (TruckAd::getRelationships() as $key => $value) {
                   $query->with($key);
                }
                
                $query->with(['ad' => function ($query)
                        {
                            $query->with(['images','user']);
                        }
                    ]);

            }
        );

        $data = $data->toArray(); 
            
        array_push($promoted,...$data['data']);
    
        $data['data'] = $promoted;
        
        
        return ['data' => $data];
    }


    public function searchLike(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'filter' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors()],422);
        }
        
        $filter = $request->filter;

        $data = TruckAd::whereRaw("ad_id in (SELECT id FROM ads where (ads.title LIKE '%".$filter."%' or ads.description LIKE '%".$filter."%') and type = 'truck')")
                    ->with(['make','fuelType','ad'=> function($query)
                    {
                        $query->with(['images']);
                    },
                'transmissionType','dealer','dealerShowRoom'])->paginate(25);

        return response()->json([
            'data' => $data
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.truck-ad.create');

        return view('admin.truck-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTruckAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'market_id' => 'required|string|exists:markets,id',
            'make_id' => 'nullable|string|exists:makes,id',
            'custom_make' => ['nullable', 'string'],
            'model' => ['required', 'string'],
            'fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'vehicle_category_id' => 'nullable|string|exists:vehicle_categories,id',
            'transmission_type_id' =>'nullable|string|exists:car_transmission_types,id',
            'drive_type_id' => 'nullable|string|exists:car_wheel_drive_types,id',
            'cab' => ['nullable', 'string'],
            'construction_year' => ['nullable', 'integer'],
            'first_registration_month' => ['nullable', 'integer'],
            'first_registration_year' => ['nullable', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'construction_height_mm' => ['nullable', 'numeric'],
            'lifting_height_mm' => ['nullable', 'numeric'],
            'lifting_capacity_kg_m' => ['nullable', 'numeric'],
            'permanent_total_weight_kg' => ['nullable', 'numeric'],
            'allowed_pulling_weight_kg' => ['nullable', 'numeric'],
            'payload_kg' => ['nullable', 'numeric'],
            'max_weight_allowed_kg' => ['nullable', 'numeric'],
            'empty_weight_kg' => ['nullable', 'numeric'],
            'loading_space_length_mm' => ['nullable', 'numeric'],
            'loading_space_width_mm' => ['nullable', 'numeric'],
            'loading_space_height_mm' => ['nullable', 'numeric'],
            'loading_volume_m3' => ['nullable', 'numeric'],
            'load_capacity_kg' => ['nullable', 'numeric'],
            'operating_weight_kg' => ['nullable', 'numeric'],
            'operating_hours' => ['nullable', 'integer'],
            'axes' => ['nullable', 'integer'],
            'wheel_formula' => ['nullable', 'string'],
            'hydraulic_system' => ['nullable', 'string'],
            'seats' => ['nullable', 'integer'],
            'mileage' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'interior_color' => ['nullable', 'string'],
            'exterior_color' => ['nullable', 'string'],
            'engine_displacement' => ['nullable', 'integer'],
            'price' => ['required', 'numeric','max:99999999'],
            'dealer_id' => ['nullable', 'string'],
            'dealer_show_room_id' => ['nullable', 'string'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email_address' => ['required', 'string'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'doors' => ['nullable', 'integer'],
            'price_contains_vat' => ['required', 'boolean'],
            
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        if (count($request->file()) > 30) {
            ApiHelper::setError($resource, 0, 422,['files' => 'Has excedido el numero maximos de imagenes']);
            return $this->sendResponse($resource);
        }

        if (count($request->file()) < 3) {
            ApiHelper::setError($resource, 0, 422,['files' => 'Debe enviar minimo 3 imagenes para publicar']);
            return $this->sendResponse($resource);
        }

        try {
            
            $slug = $this->slugAd($request['title']);

            $ad = new Ad;
            $ad->slug =  $slug;
            $ad->title =  $request['title'];
            $ad->description =  $request['description'];
            $ad->status =  0;
            $ad->type =  'truck';
            $ad->is_featured =  0;
            $ad->user_id =  Auth::user()->id;
            $ad->market_id = $request['market_id'];
            $ad->images_processing_status = $request->file() !== null ? 'SUCCESSFUL' : 'N/A';
            $ad->images_processing_status_text = null;
            $ad->save();

            
            $thumbnail = null;

            $i = 0;

            if ($request->file()) {
                foreach ($request->file() as $file) {
                    if ($i == 0) {
                        $thumbnail = $this->uploadFile($file,$ad->id,$i,true);
                    }else{
                        $this->uploadFile($file,$ad->id,$i);
                    }
                    $i++;
                }
            }

            $ad->thumbnail = $thumbnail;
            $ad->save();

            $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;
            Redis::del('truck_ads');
            Redis::del('by_user_'.Auth::user()->id.'_filter_auto');
            $truckAd = new TruckAd;
            $truckAd->ad_id = $ad->id;
            $truckAd->make_id = $request['make_id'];
            $truckAd->custom_make = $request['custom_make'];
            $truckAd->model = $request['model'];
            $truckAd->truck_type = VehicleCategory::find($request['vehicle_category_id'])->category;
            $truckAd->fuel_type_id = $request['fuel_type_id'];
            $truckAd->vehicle_category_id = $request['vehicle_category_id'];
            $truckAd->transmission_type_id = $request['transmission_type_id'];
            $truckAd->cab = $request['cab'];
            $truckAd->construction_year = $request['construction_year'];
            $truckAd->first_registration_month = $request['first_registration_month'];
            $truckAd->first_registration_year = $request['first_registration_year'];
            $truckAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $truckAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $truckAd->dealer_id =  Auth::user()->dealer_id ?? null;
            $truckAd->dealer_show_room_id = $dealer_show_room_id;
            $truckAd->owners = $request['owners'];
            $truckAd->construction_height_mm = $request['construction_height_mm'];
            $truckAd->lifting_height_mm = $request['lifting_height_mm'];
            $truckAd->lifting_capacity_kg_m = $request['lifting_capacity_kg_m'];
            $truckAd->permanent_total_weight_kg = $request['permanent_total_weight_kg'];
            $truckAd->allowed_pulling_weight_kg = $request['allowed_pulling_weight_kg'];
            $truckAd->payload_kg = $request['payload_kg'];
            $truckAd->max_weight_allowed_kg = $request['max_weight_allowed_kg'];
            $truckAd->empty_weight_kg = $request['empty_weight_kg'];
            $truckAd->loading_space_length_mm = $request['loading_space_length_mm'];
            $truckAd->loading_space_width_mm = $request['loading_space_width_mm'];
            $truckAd->loading_space_height_mm = $request['loading_space_height_mm'];
            $truckAd->loading_volume_m3 = $request['loading_volume_m3'];
            $truckAd->load_capacity_kg = $request['load_capacity_kg'];
            $truckAd->operating_weight_kg = $request['operating_weight_kg'];
            $truckAd->operating_hours = $request['operating_hours'];
            $truckAd->axes = $request['axes'];
            $truckAd->wheel_formula = $request['wheel_formula'];
            $truckAd->engine_displacement = $request['engine_displacement'];
            $truckAd->hydraulic_system = $request['hydraulic_system'];
            $truckAd->email_address = $request['email_address'];
            $truckAd->seats = $request['seats'];
            $truckAd->mileage = $request['mileage'];
            $truckAd->power_kw = $request['power_hp'];
            $truckAd->emission_class = $request['emission_class'];
            $truckAd->fuel_consumption = $request['fuel_consumption'];
            $truckAd->co2_emissions = $request['co2_emissions'];
            $truckAd->condition = $request['condition'];
            $truckAd->interior_color = $request['interior_color'];
            $truckAd->exterior_color = $request['exterior_color'];
            $truckAd->price = $request['price'];
            $truckAd->price_contains_vat = $request['price_contains_vat'];
            $truckAd->first_name = $request['first_name'];
            $truckAd->last_name = $request['last_name'];
            $truckAd->email_address = $request['email_address'];
            $truckAd->address = $request['address'];
            $truckAd->zip_code = $request['zip_code'];
            $truckAd->city = $request['city'];
            $truckAd->country = $request['country'];
            $truckAd->mobile_number = $request['mobile_number'];
            $truckAd->landline_number = $request['landline_number'];
            $truckAd->whatsapp_number = $request['whatsapp_number'];
            $truckAd->youtube_link = $request['youtube_link'];
            $truckAd->additional_vehicle_info = $request['additional_vehicle_info'];
            $truckAd->doors = $request['doors'];
            $truckAd->drive_type_id = $request['drive_type_id'];
            $truckAd->save();
            
            $ad_sub_characteristics = [];

            foreach ($request->sub_characteristic_ids as  $sub_characteristic_id) {
                $ad_sub_characteristic =  new AdSubCharacteristic;
                $ad_sub_characteristic->ad_id = $ad->id;
                $ad_sub_characteristic->sub_characteristic_id = $sub_characteristic_id;
                $ad_sub_characteristic->save();
                array_push($ad_sub_characteristics, $ad_sub_characteristic);
            }

            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));
            
            return response()->json([
                'data' => [
                    'ad' => $ad,
                    'truck_ad' =>  $truckAd, 
                    'ad_sub_characteristics' => $ad_sub_characteristics
                ]
            ], 200);

        } catch (Exception $e) {
            $ad->delete();
            ApiHelper::setError($resource, 0, 500, $e->getMessage().', Line: '.$e->getLine());
            return $this->sendResponse($resource);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param TruckAd $truck_ad
     * @throws AuthorizationException
     * @return void
     */
    public function show(TruckAd $truck_ad)
    {
        $this->authorize('admin.truck-ad.show', $truck_ad);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TruckAd $truck_ad
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TruckAd $truck_ad)
    {
        $this->authorize('admin.truck-ad.edit', $truck_ad);


        return view('admin.truck-ad.edit', [
            'truckAd' => $truck_ad,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTruckAd $request
     * @param TruckAd $truck_ad
     * @return array|RedirectResponse|Redirector
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'market_id' => 'required|string|exists:markets,id',
            'make_id' => ['nullable', 'string'],
            'custom_make' => ['nullable', 'string'],
            'model' => ['required', 'string'],
            'fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'vehicle_category_id' => 'required|string|exists:vehicle_categories,id',
            'transmission_type_id' =>'nullable|string|exists:car_transmission_types,id',
            'drive_type_id' => 'nullable|string|exists:car_wheel_drive_types,id',
            'cab' => ['nullable', 'string'],
            'construction_year' => ['nullable', 'integer'],
            'first_registration_month' => ['nullable', 'integer'],
            'first_registration_year' => ['nullable', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'construction_height_mm' => ['nullable', 'numeric'],
            'lifting_height_mm' => ['nullable', 'numeric'],
            'lifting_capacity_kg_m' => ['nullable', 'numeric'],
            'permanent_total_weight_kg' => ['nullable', 'numeric'],
            'allowed_pulling_weight_kg' => ['nullable', 'numeric'],
            'payload_kg' => ['nullable', 'numeric'],
            'max_weight_allowed_kg' => ['nullable', 'numeric'],
            'empty_weight_kg' => ['nullable', 'numeric'],
            'loading_space_length_mm' => ['nullable', 'numeric'],
            'loading_space_width_mm' => ['nullable', 'numeric'],
            'loading_space_height_mm' => ['nullable', 'numeric'],
            'loading_volume_m3' => ['nullable', 'numeric'],
            'load_capacity_kg' => ['nullable', 'numeric'],
            'operating_weight_kg' => ['nullable', 'numeric'],
            'operating_hours' => ['nullable', 'integer'],
            'axes' => ['nullable', 'integer'],
            'wheel_formula' => ['nullable', 'string'],
            'hydraulic_system' => ['nullable', 'string'],
            'seats' => ['nullable', 'integer'],
            'mileage' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'engine_displacement' => ['nullable', 'integer'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'interior_color' => ['nullable', 'string'],
            'exterior_color' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email_address' => ['required', 'string'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'image_ids' => ['nullable', 'array'],
            'eliminated_thumbnail' => ['required', 'boolean'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'doors' => ['nullable', 'integer'],
            'price_contains_vat' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        if (count($request->file()) > 30) {
            ApiHelper::setError($resource, 0, 422,['files' => 'Has excedido el numero maximos de imagenes']);
            return $this->sendResponse($resource);
        }

        try {

            $ad = Ad::where('id',$id)->first();
            $ad->title =  $request['title'];
            $ad->description =  $request['description'];
            $ad->status =  0;
            $ad->save();

            $thumbnail = null;

            $i = 0;

            
            if ($request->image_ids) {
                AdImage::whereIn('id',$request->image_ids)->delete();
            }

            if ($request->file()) {
                foreach ($request->file() as $file) {
                    if ($i == 0) {
                        if ($request->eliminated_thumbnail) {
                            $thumbnail = $this->uploadFile($file,$ad->id,$i,true);
                            $ad->thumbnail = $thumbnail;
                            $ad->save();
                        }else{
                            $this->uploadFile($file,$ad->id,$i);
                        }
                    }else{
                        $this->uploadFile($file,$ad->id,$i);
                    }
                    $i++;
                }
            }

            Redis::del('truck_ads');
            Redis::del('by_user_'.Auth::user()->id.'_filter_auto');

            $truckAd = TruckAd::where('ad_id',$id)->first();
            $truckAd->make_id = $request['make_id'];
            $truckAd->custom_make = $request['custom_make'];
            $truckAd->model = $request['model'];
            $truckAd->truck_type = VehicleCategory::find($request['vehicle_category_id'])->category;
            $truckAd->fuel_type_id = $request['fuel_type_id'];

            $truckAd->price_contains_vat = $request['price_contains_vat'];
            $truckAd->vehicle_category_id = $request['vehicle_category_id'];
            $truckAd->transmission_type_id = $request['transmission_type_id'];
            $truckAd->cab = $request['cab'];
            $truckAd->construction_year = $request['construction_year'];
            $truckAd->first_registration_month = $request['first_registration_month'];
            $truckAd->first_registration_year = $request['first_registration_year'];
            $truckAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $truckAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $truckAd->owners = $request['owners'];
            $truckAd->construction_height_mm = $request['construction_height_mm'];
            $truckAd->lifting_height_mm = $request['lifting_height_mm'];
            $truckAd->lifting_capacity_kg_m = $request['lifting_capacity_kg_m'];
            $truckAd->permanent_total_weight_kg = $request['permanent_total_weight_kg'];
            $truckAd->allowed_pulling_weight_kg = $request['allowed_pulling_weight_kg'];
            $truckAd->payload_kg = $request['payload_kg'];
            $truckAd->max_weight_allowed_kg = $request['max_weight_allowed_kg'];
            $truckAd->empty_weight_kg = $request['empty_weight_kg'];
            $truckAd->loading_space_length_mm = $request['loading_space_length_mm'];
            $truckAd->loading_space_width_mm = $request['loading_space_width_mm'];
            $truckAd->loading_space_height_mm = $request['loading_space_height_mm'];
            $truckAd->loading_volume_m3 = $request['loading_volume_m3'];
            $truckAd->load_capacity_kg = $request['load_capacity_kg'];
            $truckAd->operating_weight_kg = $request['operating_weight_kg'];
            $truckAd->operating_hours = $request['operating_hours'];
            $truckAd->axes = $request['axes'];
            $truckAd->wheel_formula = $request['wheel_formula'];
            $truckAd->hydraulic_system = $request['hydraulic_system'];
            $truckAd->email_address = $request['email_address'];
            $truckAd->seats = $request['seats'];
            $truckAd->mileage = $request['mileage'];
            $truckAd->power_kw = $request['power_hp'];
            $truckAd->engine_displacement = $request['engine_displacement'];
            $truckAd->emission_class = $request['emission_class'];
            $truckAd->fuel_consumption = $request['fuel_consumption'];
            $truckAd->co2_emissions = $request['co2_emissions'];
            $truckAd->condition = $request['condition'];
            $truckAd->interior_color = $request['interior_color'];
            $truckAd->exterior_color = $request['exterior_color'];
            $truckAd->price = $request['price'];
            $truckAd->first_name = $request['first_name'];
            $truckAd->last_name = $request['last_name'];
            $truckAd->email_address = $request['email_address'];
            $truckAd->address = $request['address'];
            $truckAd->zip_code = $request['zip_code'];
            $truckAd->city = $request['city'];
            $truckAd->country = $request['country'];
            $truckAd->mobile_number = $request['mobile_number'];
            $truckAd->landline_number = $request['landline_number'];
            $truckAd->whatsapp_number = $request['whatsapp_number'];
            $truckAd->youtube_link = $request['youtube_link'];
            $truckAd->additional_vehicle_info = $request['additional_vehicle_info'];
            $truckAd->doors = $request['doors'];
            $truckAd->drive_type_id = $request['drive_type_id'];
            $truckAd->save();
            
            $ad_sub_characteristics = [];
            
            AdSubCharacteristic::where('ad_id',$id)->first();

            foreach ($request->sub_characteristic_ids as  $sub_characteristic_id) {
                $ad_sub_characteristic =  new AdSubCharacteristic;
                $ad_sub_characteristic->ad_id = $ad->id;
                $ad_sub_characteristic->sub_characteristic_id = $sub_characteristic_id;
                $ad_sub_characteristic->save();
                array_push($ad_sub_characteristics, $ad_sub_characteristic);
            }

            //$user = Auth::user();

            //$user->notify(new \App\Notifications\NewAd($user));
            
            return response()->json([
                'data' => [
                    'ad' => $ad,
                    'truck_ad' =>  $truckAd, 
                    'ad_sub_characteristics' => $ad_sub_characteristics
                ]
            ], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage().', Line: '.$e->getLine());
            return $this->sendResponse($resource);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTruckAd $request
     * @param TruckAd $truck_ad
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTruckAd $request, TruckAd $truck_ad)
    {
        $truck_ad->delete();
        Redis::del('truck_ads');
        Redis::del('by_user_'.Auth::user()->id.'_filter_auto');
        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTruckAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTruckAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    TruckAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function uploadFile($file,$ad_id,$order_index,$thumbnail = false)
    {   
        $path = null;
        
        if ($file) {
            $path = $file->store(
                'listings/'.$ad_id, 's3'
            );
        }
        
        if (!$thumbnail) {
           AdImage::create([
                'ad_id' => $ad_id,
                'path' => $path, 
                'is_external' => 1, 
                'order_index' => $order_index
            ]);
        }
        
        return $path;
    }

    private function slugAd($title)
    {   
        $response = Str::slug($title);

        $validate = Ad::where('slug',Str::slug($title))->count();  
        
        if ($validate  != 0) {
            $response .= '-'.Str::uuid()->toString().'-'.$validate;
        }
        
        return $response;
    }

    public function truckAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','truck')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'truckAd' => function($query)
                        {
                            $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                        },
                    ]
                );

        return ['data' => $data->get()];
    }
}
