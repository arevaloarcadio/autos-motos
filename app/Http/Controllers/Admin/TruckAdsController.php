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
use App\Models\{Ad,TruckAd,DealerShowRoom,AdSubCharacteristic,AdImage};

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
        $promoted_simple_ads = TruckAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->inRandomOrder()->limit(25);

        foreach (TruckAd::getRelationships() as $key => $value) {
           $promoted_simple_ads->with($key);
        }

        $promoted = $promoted_simple_ads->get()->toArray();
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TruckAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'construction_height_mm', 'lifting_height_mm', 'lifting_capacity_kg_m', 'permanent_total_weight_kg', 'allowed_pulling_weight_kg', 'payload_kg', 'max_weight_allowed_kg', 'empty_weight_kg', 'loading_space_length_mm', 'loading_space_width_mm', 'loading_space_height_mm', 'loading_volume_m3', 'load_capacity_kg', 'operating_weight_kg', 'operating_hours', 'axes', 'wheel_formula', 'hydraulic_system', 'seats', 'mileage', 'power_kw', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'interior_color', 'exterior_color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            // set columns to searchIn
            ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'wheel_formula', 'hydraulic_system', 'emission_class', 'condition', 'interior_color', 'exterior_color', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'construction_height_mm', 'lifting_height_mm', 'lifting_capacity_kg_m', 'permanent_total_weight_kg', 'allowed_pulling_weight_kg', 'payload_kg', 'max_weight_allowed_kg', 'empty_weight_kg', 'loading_space_length_mm', 'loading_space_width_mm', 'loading_space_height_mm', 'loading_volume_m3', 'load_capacity_kg', 'operating_weight_kg', 'operating_hours', 'axes', 'wheel_formula', 'hydraulic_system', 'seats', 'mileage', 'power_kw', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'interior_color', 'exterior_color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
                
                    if ($request->filters) {
                        foreach ($columns as $column) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (TruckAd::getRelationships() as $key => $value) {
                   $query->with($key);
                }
                $query->with([
                    'ad' => function($query)
                    {
                        $query->with(['images']);
                    }
                ]);
            }
        );

        $data = $data->toArray(); 
            
        array_push($promoted,...$data['data']);
    
        $data['data'] = $promoted;
        
        
        return ['data' => $data];
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
    public function store(StoreTruckAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        $ad = Ad::create([
            'slug' => Str::slug($sanitized['title']),
            'title' => $sanitized['title'],
            'description' => $sanitized['description'],
            'thumbnail' => $sanitized['thumbnail'],
            'status' => 0,
            'type' => 'truck',
            'is_featured' => 0,
            'user_id' => Auth::user()->id,
            'market_id' => $sanitized['market_id'],
            'external_id' =>null,
            'source' => null,
            'images_processing_status' => 'SUCCESSFUL',
            'images_processing_status_text' => null,
        ]);
        
        $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id']  : null;
        // Store the TruckAd
        $truck_ad = TruckAd::create([
            'ad_id' =>  $ad->id,
            'make_id' => $sanitized['make_id'],
            'custom_make' => $sanitized['custom_make'],
            'model' => $sanitized['model'],
            'truck_type' => $sanitized['truck_type'],
            'fuel_type_id' => $sanitized['fuel_type_id'],
            'vehicle_category_id' => $sanitized['vehicle_category_id'],
            'transmission_type_id' => $sanitized['transmission_type_id'],
            'cab' => $sanitized['cab'],
            'construction_year' => $sanitized['construction_year'],
            'first_registration_month' => $sanitized['first_registration_month'],
            'first_registration_year' => $sanitized['first_registration_year'],
            'inspection_valid_until_month' => $sanitized['inspection_valid_until_month'],
            'inspection_valid_until_year' => $sanitized['inspection_valid_until_year'],
            'owners' => $sanitized['owners'],
            'construction_height_mm' => $sanitized['construction_height_mm'],
            'lifting_height_mm' => $sanitized['lifting_height_mm'],
            'lifting_capacity_kg_m' => $sanitized['lifting_capacity_kg_m'],
            'permanent_total_weight_kg' => $sanitized['permanent_total_weight_kg'],
            'allowed_pulling_weight_kg' => $sanitized['allowed_pulling_weight_kg'],
            'payload_kg' => $sanitized['payload_kg'],
            'max_weight_allowed_kg' => $sanitized['max_weight_allowed_kg'],
            'empty_weight_kg' => $sanitized['empty_weight_kg'],
            'loading_space_length_mm' =>$sanitized['loading_space_length_mm'],
            'loading_space_width_mm' => $sanitized['loading_space_width_mm'],
            'loading_space_height_mm' =>  $sanitized['loading_space_height_mm'],
            'loading_volume_m3' => $sanitized['loading_volume_m3'],
            'load_capacity_kg' =>$sanitized['load_capacity_kg'],
            'operating_weight_kg' => $sanitized['operating_weight_kg'],
            'operating_hours' => $sanitized['operating_hours'],
            'axes' => $sanitized['axes'],
            'wheel_formula' => $sanitized['wheel_formula'],
            'hydraulic_system' => $sanitized['hydraulic_system'],
            'seats' => $sanitized['seats'],
            'mileage' => $sanitized['mileage'],
            'power_kw' => $sanitized['power_kw'],
            'emission_class' => $sanitized['emission_class'],
            'fuel_consumption' => $sanitized['fuel_consumption'],
            'co2_emissions' => $sanitized['co2_emissions'],
            'condition' => $sanitized['condition'],
            'interior_color' => $sanitized['interior_color'],
            'exterior_color' => $sanitized['exterior_color'],
            'price' => $sanitized['price'],
            'price_contains_vat' => $sanitized['price_contains_vat'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' => $sanitized['first_name'],
            'last_name' => $sanitized['last_name'],
            'email_address' => $sanitized['email_address'],
            'address' => $sanitized['address'],
            'zip_code' => $sanitized['zip_code'],
            'city' => $sanitized['city'],
            'country' => $sanitized['country'],
            'mobile_number' => $sanitized['mobile_number'],
            'landline_number' => $sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' => $sanitized['youtube_link'],
            
        ]);

        return ['data' => ['ad' => $ad,'truck_ad' => $truck_ad]];
    }


    public function principal_data(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'make_id' => ['required', 'string'],
            'model' => ['required', 'string'],
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'generation_id' => ['nullable', 'string'],
            'mileage' => ['required', 'integer'],
            'condition' => ['required', 'string'],
            'exterior_color' => ['required', 'string'],
            'interior_color' => ['nullable', 'string'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'ad_fuel_type_id' => ['nullable', 'string'],
            'ad_transmission_type_id' => ['nullable', 'string'],
            'ad_drive_type_id' => ['nullable', 'string'],
            'engine_displacement' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'doors' => ['nullable', 'integer'],
            'seats' => ['nullable', 'integer'],
            'truck_type' => ['required', 'string'],
            'vehicle_category_id' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
            
            //INSERT INTO `ads` (`id`, `slug`, `title`, `description`, `thumbnail`, `status`, `type`, `is_featured`, `user_id`, `market_id`, `created_at`, `updated_at`, `external_id`, `source`, `images_processing_status`, `images_processing_status_text`, `csv_ad_id`) VALUES ('.', '.', '.', '.', '.', '0', '', '0', '23bcf97c-296b-46c9-bdd5-8057e052bfce', '5b8fa498-efe4-4c19-90a8-7285901b4585', '2022-07-02 09:28:15', '2021-05-15 09:28:15', '26409', NULL, 'N/A', NULL, NULL);

            $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;

            $truck_ad = TruckAd::create([
                'ad_id' =>  '.',
                'email_address' =>  '.',
                'address' =>  '.',
                'zip_code' =>  '.',
                'city' =>  '.',
                'country' =>  '.',
                'price' =>  0,
                'truck_type' => $request['truck_type'],
                'doors' => $request['doors'],
                'mileage' => $request['mileage'],
                'exterior_color' => $request['exterior_color'],
                'interior_color' =>$request['interior_color'],
                'condition' => $request['condition'],
                'dealer_id' => Auth::user()->dealer_id ?? null,
                'dealer_show_room_id' => $dealer_show_room_id,
                'ad_fuel_type_id' =>  $request['ad_fuel_type_id'],
                'ad_transmission_type_id' =>  $request['ad_transmission_type_id'],
                'vehicle_category_id' =>  $request['vehicle_category_id'],
                'ad_drive_type_id' =>  $request['ad_drive_type_id'],
                'first_registration_month' =>  $request['first_registration_month'],
                'first_registration_year' =>  $request['first_registration_year'],
                'engine_displacement' =>  $request['engine_displacement'],
                'power_hp' => $request['power_hp'],
                'inspection_valid_until_month' =>  $request['inspection_valid_until_month'],
                'inspection_valid_until_year' => $request['inspection_valid_until_year'],
                'make_id' =>  $request['make_id'],
                'model' => $request['model'],
                'generation_id' =>  $request['generation_id'],
                'additional_vehicle_info' =>  $request['additional_vehicle_info'],
                'seats' =>  $request['seats'],
                'fuel_consumption' =>  $request['fuel_consumption'],
                'co2_emissions' => $request['co2_emissions'],
            ]);

            return response()->json(['data' => $truck_ad], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


    public function details_ads(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'truck_ad_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'market_id' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
            
            $slug = $this->slugAd($request['title']);

            $ad = Ad::create([
                'slug' => $slug,
                'title' => $request['title'],
                'description' => $request['description'],
                //'thumbnail' => $request['thumbnail'],
                'status' => 0,
                'type' => 'truck',
                'is_featured' => 0,
                'user_id' => Auth::user()->id,
                'market_id' => '5b8fa498-efe4-4c19-90a8-7285901b4585',
                'external_id' =>null,
                'source' => null,
                'images_processing_status' => 'N/A',
                'images_processing_status_text' => null,
            ]);

            $thumbnail = '';
            $i = 0;
            
            if ($request->file()) {
                foreach ($request->file() as $file) {
                    if ($i == 0) {
                        $thumbnail = $this->uploadFile($file,$ad->id,$i);
                    }else{
                        $this->uploadFile($file,$ad->id,$i);
                    }
                    $i++;
                }
            }

            TruckAd::where('id',$request['truck_ad_id'])->update([
                'ad_id' =>  $ad->id,
                'youtube_link' =>  $request->youtube_link,
                'price' =>  $request->price,
            ]);

            $truck_ad = TruckAd::find($request['truck_ad_id']);
            
            Ad::where('id',$ad->id)->update(['thumbnail' => $thumbnail]);
            
            $images = AdImage::where('ad_id',$ad->id)->get();
            
            return response()->json(['data' => ['ad' => $ad,'truck_ad' =>$truck_ad,'images' => $images]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function add_sub_characteristic_ads(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'sub_characteristics' => ['required', 'array'],
            'sub_characteristics.*.ad_id' => ['required', 'string'],
            'sub_characteristics.*.sub_characteristic_id' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
           // dd($request->sub_characteristics);
            $response = [];

            foreach ($request->sub_characteristics as  $sub_characteristics) {
                $ad_sub_characteristic =  new AdSubCharacteristic;
                $ad_sub_characteristic->ad_id = $sub_characteristics['ad_id'];
                $ad_sub_characteristic->sub_characteristic_id = $sub_characteristics['sub_characteristic_id'];
                $ad_sub_characteristic->save();

                array_push($response, $ad_sub_characteristic);
            }
            
            return response()->json(['data' => $response], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function add_details_contacts(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'truck_ad_id' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email_address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'address' => ['required', 'string'],
            'mobile_number' => ['required', 'string'],
            'whatsapp_number' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
            
            TruckAd::where('id',$request['truck_ad_id'])->update([
                'first_name' =>  $request->first_name,
                'last_name' =>  $request->last_name,
                'email_address' =>  $request->email_address,
                'zip_code' =>  $request->zip_code,
                'city' =>  $request->city,
                'country' =>  $request->country,
                'address' =>  $request->address,
                'mobile_number' =>  $request->mobile_number,
                'whatsapp_number' =>  $request->whatsapp_number,
            ]);

            $truck_ad = TruckAd::find($request['truck_ad_id']);
            
            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));
            
            return response()->json(['data' => $truck_ad], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
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
    public function update(UpdateTruckAd $request, TruckAd $truck_ad)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values TruckAd
        $truck_ad->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/truck-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/truck-ads');
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

    public function uploadFile($file,$ad_id,$order_index)
    {   
        $path = null;
        
        if ($file) {
            $path = $file->store(
                'listings/'.$ad_id, 's3'
            );
        }
        
        AdImage::create([
            'ad_id' => $ad_id,
            'path' => $path, 
            'is_external' => 1, 
            'order_index' => $order_index
        ]);

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
