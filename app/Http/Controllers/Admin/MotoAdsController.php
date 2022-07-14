<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MotoAd\BulkDestroyMotoAd;
use App\Http\Requests\Admin\MotoAd\DestroyMotoAd;
use App\Http\Requests\Admin\MotoAd\IndexMotoAd;
use App\Http\Requests\Admin\MotoAd\StoreMotoAd;
use App\Http\Requests\Admin\MotoAd\UpdateMotoAd;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use Illuminate\Http\Request;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Ad,MotoAd,DealerShowRoom,AdSubCharacteristic,AdImage};

class MotoAdsController extends Controller
{
    use ApiController;
    /**
     * Display a listing of the resource.
     *
     * @param IndexMotoAd $request
     * @return array|Factory|View
     */
    public function index(IndexMotoAd $request)
    {
        if ($request->all) {
            
            $query = MotoAd::query();

            $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'last_customer_service_month', 'last_customer_service_year', 'owners', 'weight_kg', 'engine_displacement', 'mileage', 'power_kw', 'gears', 'cylinders', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (MotoAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MotoAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'last_customer_service_month', 'last_customer_service_year', 'owners', 'weight_kg', 'engine_displacement', 'mileage', 'power_kw', 'gears', 'cylinders', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            // set columns to searchIn
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'emission_class', 'condition', 'color', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'last_customer_service_month', 'last_customer_service_year', 'owners', 'weight_kg', 'engine_displacement', 'mileage', 'power_kw', 'gears', 'cylinders', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
                
                    if ($request->filters) {
                        foreach ($columns as $column) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (MotoAd::getRelationships() as $key => $value) {
                   $query->with($key);
                }
            }
        );
        
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
        $this->authorize('admin.moto-ad.create');

        return view('admin.moto-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMotoAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMotoAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        $ad = Ad::create([
            'slug' => Str::slug($sanitized['title']),
            'title' => $sanitized['title'],
            'description' => $sanitized['description'],
            'thumbnail' => $sanitized['thumbnail'],
            'status' => 0,
            'type' => 'moto',
            'is_featured' => 0,
            'user_id' => Auth::user()->id,
            'market_id' => $sanitized['market_id'],
            'external_id' =>null,
            'source' => null,
            'images_processing_status' => 'SUCCESSFUL',
            'images_processing_status_text' => null,
        ]);
        
        $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id']  : null;
        // Store the MotoAd
        $motoAd = MotoAd::create([
            'ad_id' =>  $ad->id,
            'make_id' => $sanitized['make_id'],
            'custom_make' => $sanitized['custom_make'],
            'model_id' => $sanitized['model_id'],
            'custom_model' => $sanitized['custom_model'],
            'fuel_type_id' => $sanitized['fuel_type_id'],
            'body_type_id' => $sanitized['body_type_id'],
            'transmission_type_id' => $sanitized['transmission_type_id'],
            'drive_type_id' => $sanitized['drive_type_id'],
            'first_registration_month' => $sanitized['first_registration_month'],
            'first_registration_year' => $sanitized['first_registration_year'],
            'inspection_valid_until_month' => $sanitized['inspection_valid_until_month'],
            'inspection_valid_until_year' => $sanitized['inspection_valid_until_year'],
            'last_customer_service_month' => $sanitized['last_customer_service_month'],
            'last_customer_service_year' => $sanitized['last_customer_service_year'],
            'owners' => $sanitized['owners'],
            'weight_kg' => $sanitized['weight_kg'],
            'engine_displacement' => $sanitized['engine_displacement'],
            'mileage' =>$sanitized['mileage'],
            'power_kw' => $sanitized['power_kw'],
            'gears' => $sanitized['gears'],
            'cylinders' => $sanitized['cylinders'],
            'emission_class' => $sanitized['emission_class'],
            'fuel_consumption' => $sanitized['fuel_consumption'],
            'co2_emissions' => $sanitized['co2_emissions'],
            'condition' =>$sanitized['condition'],
            'color' =>$sanitized['color'],
            'price' =>$sanitized['price'],
            'price_contains_vat' => $sanitized['price_contains_vat'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' => $sanitized['first_name'],
            'last_name' => $sanitized['last_name'],
            'email_address' => $sanitized['email_address'],
            'address' => $sanitized['address'],
            'zip_code' => $sanitized['zip_code'],
            'city' => $sanitized['city'],
            'country' =>$sanitized['country'],
            'mobile_number' => $sanitized['mobile_number'],
            'landline_number' =>$sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' =>$sanitized['youtube_link']
        ]);

        return ['data' => ['ad' => $ad, 'moto_ad' => $motoAd]];
    }

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
            'fuel_type_id' => ['nullable', 'string'],
            'transmission_type_id' => ['nullable', 'string'],
            'body_type_id' => ['required', 'string'],
            'drive_type_id' => ['nullable', 'string'],
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

            
            $motoAd = MotoAd::create([
                'ad_id' =>  '.',
                'email_address' =>  '.',
                'address' =>  '.',
                'zip_code' =>  '.',
                'city' =>  '.',
                'country' =>  '.',
                'color' =>  '.',
                'price' =>  0,
                'doors' => $request['doors'],
                'mileage' => $request['mileage'],
                'exterior_color' => $request['exterior_color'],
                'interior_color' =>$request['interior_color'],
                'condition' => $request['condition'],
                'dealer_id' => Auth::user()->dealer_id ?? null,
                'dealer_show_room_id' => $dealer_show_room_id,
                'fuel_type_id' =>  $request['fuel_type_id'],
                'transmission_type_id' =>  $request['transmission_type_id'],
                'drive_type_id' =>  $request['drive_type_id'],
                'body_type_id' =>  $request['body_type_id'],
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

            return response()->json(['data' => $motoAd], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


    public function details_ads(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'moto_ad_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'market_id' => ['required', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
            
            $ad = Ad::create([
                'slug' => Str::slug($request['title']),
                'title' => $request['title'],
                'description' => $request['description'],
                //'thumbnail' => $request['thumbnail'],
                'status' => 0,
                'type' => 'moto',
                'is_featured' => 0,
                'user_id' => Auth::user()->id,
                'market_id' => $request['market_id'],
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


            MotoAd::where('id',$request['moto_ad_id'])->update([
                'ad_id' =>  $ad->id,
                'youtube_link' =>  $request->youtube_link,
                'price' =>  $request->price,
            ]);

            $motoAd = MotoAd::find($request['moto_ad_id']);
            
            Ad::where('id',$ad->id)->update(['thumbnail' => $thumbnail]);
            
            $images = AdImage::where('ad_id',$ad->id)->get();

            return response()->json(['data' => ['ad' => $ad,'moto_ad' =>$motoAd,'images' => $images]], 200);

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
            'moto_ad_id' => ['required', 'string'],
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
            
            MotoAd::where('id',$request['moto_ad_id'])->update([
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

            $motoAd = MotoAd::find($request['moto_ad_id']);
           
            return response()->json(['data' => $motoAd], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param MotoAd $motoAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(MotoAd $motoAd)
    {
        $this->authorize('admin.moto-ad.show', $motoAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MotoAd $motoAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MotoAd $motoAd)
    {
        $this->authorize('admin.moto-ad.edit', $motoAd);


        return view('admin.moto-ad.edit', [
            'motoAd' => $motoAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMotoAd $request
     * @param MotoAd $motoAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMotoAd $request, MotoAd $motoAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MotoAd
        $motoAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/moto-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/moto-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMotoAd $request
     * @param MotoAd $motoAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMotoAd $request, MotoAd $motoAd)
    {
        $motoAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMotoAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMotoAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MotoAd::whereIn('id', $bulkChunk)->delete();

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
}
