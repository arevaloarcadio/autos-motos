<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AutoAd\BulkDestroyAutoAd;
use App\Http\Requests\Admin\AutoAd\DestroyAutoAd;
use App\Http\Requests\Admin\AutoAd\IndexAutoAd;
use App\Http\Requests\Admin\AutoAd\StoreAutoAd;
use App\Http\Requests\Admin\AutoAd\UpdateAutoAd;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Illuminate\Http\Request;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Ad,AutoAd,DealerShowRoom,AdSubCharacteristic,AdImage};

class AutoAdsController extends Controller
{
     use ApiController;

    /**
     * Display a listing of the resource.
     *
     * @param IndexAutoAd $request
     * @return array|Factory|View
     */
    public function index(IndexAutoAd $request)
    {
        $promoted_simple_ads = AutoAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->inRandomOrder()->limit(25);

        foreach (AutoAd::getRelationships() as $key => $value) {
           $promoted_simple_ads->with($key);
        }

        $promoted = $promoted_simple_ads->get()->toArray();
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AutoAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'price', 'price_contains_vat', 'vin', 'doors', 'mileage', 'exterior_color', 'interior_color', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link', 'ad_fuel_type_id', 'ad_body_type_id', 'ad_transmission_type_id', 'ad_drive_type_id', 'first_registration_month', 'first_registration_year', 'engine_displacement', 'power_hp', 'owners', 'inspection_valid_until_month', 'inspection_valid_until_year', 'make_id', 'model_id', 'generation_id', 'series_id', 'trim_id', 'equipment_id', 'additional_vehicle_info', 'seats', 'fuel_consumption', 'co2_emissions', 'latitude', 'longitude', 'geocoding_status'],

            // set columns to searchIn
            ['id', 'ad_id', 'vin', 'exterior_color', 'interior_color', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link', 'make_id', 'model_id', 'equipment_id', 'additional_vehicle_info', 'latitude', 'longitude', 'geocoding_status'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'ad_id', 'price', 'price_contains_vat', 'vin', 'doors', 'mileage', 'exterior_color', 'interior_color', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link', 'ad_fuel_type_id', 'ad_body_type_id', 'ad_transmission_type_id', 'ad_drive_type_id', 'first_registration_month', 'first_registration_year', 'engine_displacement', 'power_hp', 'owners', 'inspection_valid_until_month', 'inspection_valid_until_year', 'make_id', 'model_id', 'generation_id', 'series_id', 'trim_id', 'equipment_id', 'additional_vehicle_info', 'seats', 'fuel_consumption', 'co2_emissions', 'latitude', 'longitude', 'geocoding_status'];
                
                
                if ($request->filters) {
                    foreach ($columns as $column) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (AutoAd::getRelationships() as $key => $value) {
                   $query->with($key);
                }
                
                $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)');

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
        $this->authorize('admin.auto-ad.create');

        return view('admin.auto-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAutoAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAutoAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        $slug = $this->slugAd($sanitized['title']);

            $ad = Ad::create([
            'slug' => $slug,
            'title' => $sanitized['title'],
            'description' => $sanitized['description'],
            'thumbnail' => $sanitized['thumbnail'],
            'status' => 0,
            'type' => 'auto',
            'is_featured' => 0,
            'user_id' => Auth::user()->id,
            'market_id' => $sanitized['market_id'],
            'external_id' =>null,
            'source' => null,
            'images_processing_status' => 'SUCCESSFUL',
            'images_processing_status_text' => null,
        ]);
        
        $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;

        $autoAd = AutoAd::create([
            'ad_id' =>  $ad->id,
            'price' => $sanitized['price'],
            'price_contains_vat' => 0,
            'vin' => null,
            'doors' => $sanitized['doors'],
            'mileage' => $sanitized['mileage'],
            'exterior_color' => $sanitized['exterior_color'],
            'interior_color' =>$sanitized['interior_color'],
            'condition' => $sanitized['condition'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' =>  $sanitized['first_name'],
            'last_name' =>  $sanitized['last_name'],
            'email_address' =>  $sanitized['email_address'],
            'address' =>  $sanitized['address'],
            'zip_code' =>  $sanitized['zip_code'],
            'city' =>  $sanitized['city'],
            'country' =>  $sanitized['country'],
            'mobile_number' =>  $sanitized['mobile_number'],
            'landline_number' =>  $sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' => $sanitized['youtube_link'],
            'ad_fuel_type_id' =>  $sanitized['ad_fuel_type_id'],
            'ad_body_type_id' =>  $sanitized['ad_body_type_id'],
            'ad_transmission_type_id' =>  $sanitized['ad_transmission_type_id'],
            'ad_drive_type_id' =>  $sanitized['ad_drive_type_id'],
            'first_registration_month' =>  $sanitized['first_registration_month'],
            'first_registration_year' =>  $sanitized['first_registration_year'],
            'engine_displacement' =>  $sanitized['engine_displacement'],
            'power_hp' => $sanitized['power_hp'],
            'owners' =>  $sanitized['owners'],
            'inspection_valid_until_month' =>  $sanitized['inspection_valid_until_month'],
            'inspection_valid_until_year' => $sanitized['inspection_valid_until_year'],
            'make_id' =>  $sanitized['make_id'],
            'model_id' => $sanitized['model_id'],
            'generation_id' =>  $sanitized['generation_id'],
            'series_id' =>  $sanitized['series_id'],
            'trim_id' => $sanitized['trim_id'],
            'equipment_id' =>  $sanitized['equipment_id'],
            'additional_vehicle_info' =>  $sanitized['additional_vehicle_info'],
            'seats' =>  $sanitized['seats'],
            'fuel_consumption' =>  $sanitized['fuel_consumption'],
            'co2_emissions' => $sanitized['co2_emissions'],
            'latitude' =>  $sanitized['latitude'],
            'longitude' =>  $sanitized['longitude'],
            'geocoding_status' =>  $sanitized['geocoding_status'],
            
        ]);

        return ['data' => ['ad' => $ad,'auto_ad' =>$autoAd]];
    }

    /**
     * Display the specified resource.
     *
     * @param AutoAd $autoAd
     * @throws AuthorizationException
     * @return void
     */

    public function principal_data(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'make_id' => ['required', 'string'],
            'model_id' => ['required', 'string'],
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
            'seats' => ['nullable', 'integer']
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
            
            //INSERT INTO `ads` (`id`, `slug`, `title`, `description`, `thumbnail`, `status`, `type`, `is_featured`, `user_id`, `market_id`, `created_at`, `updated_at`, `external_id`, `source`, `images_processing_status`, `images_processing_status_text`, `csv_ad_id`) VALUES ('.', '.', '.', '.', '.', '0', '', '0', '23bcf97c-296b-46c9-bdd5-8057e052bfce', '5b8fa498-efe4-4c19-90a8-7285901b4585', '2022-07-02 09:28:15', '2021-05-15 09:28:15', '26409', NULL, 'N/A', NULL, NULL);

            $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;

            $autoAd = AutoAd::create([
                'ad_id' =>  '.',
                'email_address' =>  '.',
                'address' =>  '.',
                'zip_code' =>  '.',
                'city' =>  '.',
                'country' =>  '.',
                'price' =>  0,
                'doors' => $request['doors'],
                'mileage' => $request['mileage'],
                'exterior_color' => $request['exterior_color'],
                'interior_color' =>$request['interior_color'],
                'condition' => $request['condition'],
                'dealer_id' => Auth::user()->dealer_id ?? null,
                'dealer_show_room_id' => $dealer_show_room_id,
                'ad_fuel_type_id' =>  $request['ad_fuel_type_id'],
                'ad_transmission_type_id' =>  $request['ad_transmission_type_id'],
                'ad_drive_type_id' =>  $request['ad_drive_type_id'],
                'first_registration_month' =>  $request['first_registration_month'],
                'first_registration_year' =>  $request['first_registration_year'],
                'engine_displacement' =>  $request['engine_displacement'],
                'power_hp' => $request['power_hp'],
                'inspection_valid_until_month' =>  $request['inspection_valid_until_month'],
                'inspection_valid_until_year' => $request['inspection_valid_until_year'],
                'make_id' =>  $request['make_id'],
                'model_id' => $request['model_id'],
                'generation_id' =>  $request['generation_id'],
                'additional_vehicle_info' =>  $request['additional_vehicle_info'],
                'seats' =>  $request['seats'],
                'fuel_consumption' =>  $request['fuel_consumption'],
                'co2_emissions' => $request['co2_emissions'],
            ]);

            return response()->json(['data' => $autoAd], 200);

        } catch (Exception $e) {

            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


    public function details_ads(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'auto_ad_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            //'thumbnail' => ['nullable', 'string'],
            'market_id' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            //'files' => ['required','array']
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
                'type' => 'auto',
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

            AutoAd::where('id',$request['auto_ad_id'])->update([
                'ad_id' =>  $ad->id,
                'youtube_link' =>  $request->youtube_link,
                'price' =>  $request->price,
            ]);

            Ad::where('id',$ad->id)->update(['thumbnail' => $thumbnail]);
            
            $autoAd = AutoAd::find($request['auto_ad_id']);

            $images = AdImage::where('ad_id',$ad->id)->get();

            return response()->json(['data' => ['ad' => $ad,'auto_ad' => $autoAd,'images' => $images]], 200);

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
            'auto_ad_id' => ['required', 'string'],
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
            
            AutoAd::where('id',$request['auto_ad_id'])->update([
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

            $autoAd = AutoAd::find($request['auto_ad_id']);
            
            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));
            
            return response()->json(['data' => $autoAd], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function show(AutoAd $autoAd)
    {
        $this->authorize('admin.auto-ad.show', $autoAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AutoAd $autoAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AutoAd $autoAd)
    {
        $this->authorize('admin.auto-ad.edit', $autoAd);


        return view('admin.auto-ad.edit', [
            'autoAd' => $autoAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAutoAd $request
     * @param AutoAd $autoAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAutoAd $request, AutoAd $autoAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AutoAd
        $autoAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/auto-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/auto-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAutoAd $request
     * @param AutoAd $autoAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAutoAd $request, AutoAd $autoAd)
    {
        $autoAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAutoAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAutoAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AutoAd::whereIn('id', $bulkChunk)->delete();

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

    public function autoAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','auto')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'autoAd' => function($query)
                        {
                            $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        }
                    ]
                );

        return ['data' => $data->get()];
    }
}
