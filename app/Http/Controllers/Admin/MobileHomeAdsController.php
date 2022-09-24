<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MobileHomeAd\BulkDestroyMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\DestroyMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\IndexMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\StoreMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\UpdateMobileHomeAd;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
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
use App\Models\{Ad,MobileHomeAd,DealerShowRoom,AdSubCharacteristic,AdImage};
use Illuminate\Support\Facades\Redis as Redis;


class MobileHomeAdsController extends Controller
{
    use ApiController;

    /**
     * Display a listing of the resource.
     *
     * @param IndexMobileHomeAd $request
     * @return array|Factory|View
     */
    public function index(IndexMobileHomeAd $request)
    {
        /*$promoted_simple_ads = MobileHomeAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)')->inRandomOrder()->limit(25);

        foreach (MobileHomeAd::getRelationships() as $key => $value) {
           $promoted_simple_ads->with($key);
        }

        $promoted = $promoted_simple_ads->get()->toArray();*/
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MobileHomeAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'length_cm', 'width_cm', 'height_cm', 'max_weight_allowed_kg', 'payload_kg', 'engine_displacement', 'mileage', 'power_kw', 'axes', 'seats', 'sleeping_places', 'beds', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','doors','additional_vehicle_info','generation','drive_type_id'],

            // set columns to searchIn
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'beds', 'emission_class', 'condition', 'color', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','doors','additional_vehicle_info','generation','drive_type_id'],
            
            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'length_cm', 'width_cm', 'height_cm', 'max_weight_allowed_kg', 'payload_kg', 'engine_displacement', 'mileage', 'power_kw', 'axes', 'seats', 'sleeping_places', 'beds', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','doors','additional_vehicle_info','generation','drive_type_id'];
                
                
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
                
                foreach (MobileHomeAd::getRelationships() as $key => $value) {
                   $query->with($key);
                }

                $query->with([
                    'ad' => function($query)
                    {
                        $query->with(['images','user']);
                    }
                ]);
            }
        );

        //$data = $data->toArray(); 
            
        //array_push($promoted,...$data['data']);
    
        //$data['data'] = $promoted;
        
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
        
        $data = MobileHomeAd::whereRaw("ad_id in (SELECT id FROM ads where (ads.title LIKE '%".$filter."%' or ads.description LIKE '%".$filter."%') and type = 'mobile-home')")
                    ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images']);
                    },
                    'make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom'])->paginate(25);

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
        $this->authorize('admin.mobile-home-ad.create');

        return view('admin.mobile-home-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMobileHomeAd $request
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
            'model_id' => 'nullable|string|exists:models,id',
            'model' => ['nullable', 'string'],
            'fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'vehicle_category_id' => 'required|string|exists:vehicle_categories,id',
            'transmission_type_id' =>'nullable|string|exists:car_transmission_types,id',
            'construction_year' => ['nullable', 'integer'],
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'length_cm' => ['nullable', 'numeric'],
            'width_cm' => ['nullable', 'numeric'],
            'height_cm' => ['nullable', 'numeric'],
            'max_weight_allowed_kg' => ['nullable', 'numeric'],
            'payload_kg' => ['nullable', 'numeric'],
            'engine_displacement' => ['nullable', 'integer'],
            'mileage' => ['required', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'axes' => ['nullable', 'integer'],
            'seats' => ['nullable', 'integer'],
            'sleeping_places' => ['nullable', 'integer'],
            'beds' => ['nullable', 'string'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'color' => ['nullable', 'string'],
            'price' => ['required', 'numeric','max:99999999'],
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
            'doors' => ['nullable', 'integer'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'drive_type_id' => 'nullable|string|exists:car_wheel_drive_types,id',
            'generation_id' => 'nullable|string',
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
            Redis::del('by_user_'.Auth::user()->id.'_filter_auto');

            $ad = new Ad;
            $ad->slug =  $slug;
            $ad->title =  $request['title'];
            $ad->description =  $request['description'];
            $ad->status =  0;
            $ad->type =  'mobile-home';
            $ad->is_featured =  0;
            $ad->user_id =  Auth::user()->id;
            $ad->market_id = $request['market_id'];
            $ad->images_processing_status = $request->file() !== null ? 'SUCCESSFUL' : 'N/A';
            $ad->images_processing_status_text = null;
            $ad->save();

            $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;

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

            $mobileHomeAd = new MobileHomeAd;
            $mobileHomeAd->ad_id = $ad->id;
            $mobileHomeAd->make_id = $request['make_id'];
            $mobileHomeAd->price_contains_vat = $request['price_contains_vat'];
            $mobileHomeAd->custom_make = $request['custom_make'];
            $mobileHomeAd->model_id = $request['model_id'];
            $mobileHomeAd->custom_model = $request['model'];
            $mobileHomeAd->fuel_type_id = $request['fuel_type_id'];
            $mobileHomeAd->vehicle_category_id = $request['vehicle_category_id'];
            $mobileHomeAd->transmission_type_id = $request['transmission_type_id'];
            $mobileHomeAd->construction_year = $request['construction_year'];
            $mobileHomeAd->first_registration_month = $request['first_registration_month'];
            $mobileHomeAd->first_registration_year = $request['first_registration_year'];
            $mobileHomeAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $mobileHomeAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $mobileHomeAd->owners = $request['owners'];
            $mobileHomeAd->dealer_id =  Auth::user()->dealer_id ?? null;
            $mobileHomeAd->dealer_show_room_id = $dealer_show_room_id;
            $mobileHomeAd->length_cm = $request['length_cm'];
            $mobileHomeAd->width_cm = $request['width_cm'];
            $mobileHomeAd->height_cm = $request['height_cm'];
            $mobileHomeAd->max_weight_allowed_kg = $request['max_weight_allowed_kg'];
            $mobileHomeAd->engine_displacement = $request['engine_displacement'];
            $mobileHomeAd->mileage = $request['mileage'];
            $mobileHomeAd->power_kw = $request['power_hp'];
            $mobileHomeAd->axes = $request['axes'];
            $mobileHomeAd->seats = $request['seats'];
            $mobileHomeAd->sleeping_places = $request['sleeping_places'];
            $mobileHomeAd->beds = $request['beds'];
            $mobileHomeAd->emission_class = $request['emission_class'];
            $mobileHomeAd->fuel_consumption = $request['fuel_consumption'];
            $mobileHomeAd->co2_emissions = $request['co2_emissions'];
            $mobileHomeAd->condition = $request['condition'];
            $mobileHomeAd->color = $request['color'];
            $mobileHomeAd->price = $request['price'];
            $mobileHomeAd->first_name = $request['first_name'];
            $mobileHomeAd->last_name = $request['last_name'];
            $mobileHomeAd->email_address = $request['email_address'];
            $mobileHomeAd->address = $request['address'];
            $mobileHomeAd->zip_code = $request['zip_code'];
            $mobileHomeAd->city = $request['city'];
            $mobileHomeAd->country = $request['country'];
            $mobileHomeAd->zip_code = $request['zip_code'];
            $mobileHomeAd->mobile_number = $request['mobile_number'];
            $mobileHomeAd->landline_number = $request['landline_number'];
            $mobileHomeAd->whatsapp_number = $request['whatsapp_number'];
            $mobileHomeAd->youtube_link = $request['youtube_link'];
            $mobileHomeAd->doors = $request['doors'];
            $mobileHomeAd->additional_vehicle_info = $request['additional_vehicle_info'];
            $mobileHomeAd->drive_type_id = $request['drive_type_id'];
            $mobileHomeAd->generation = $request['generation_id'];
            $mobileHomeAd->save();
            
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
                    'mobile_home_ad' =>  $mobileHomeAd, 
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
     * @param MobileHomeAd $mobile_home_ad
     * @throws AuthorizationException
     * @return void
     */
    public function show(MobileHomeAd $mobile_home_ad)
    {
        $this->authorize('admin.mobile-home-ad.show', $mobile_home_ad);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MobileHomeAd $mobile_home_ad
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MobileHomeAd $mobile_home_ad)
    {
        $this->authorize('admin.mobile-home-ad.edit', $mobile_home_ad);


        return view('admin.mobile-home-ad.edit', [
            'mobileHomeAd' => $mobile_home_ad,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMobileHomeAd $request
     * @param MobileHomeAd $mobile_home_ad
     * @return array|RedirectResponse|Redirector
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'market_id' => 'required|string|exists:markets,id',
            'make_id' => ['nullable', 'string'],
            'custom_make' => ['nullable', 'string'],
            'model_id' => ['nullable', 'string'],
            'custom_model' => ['nullable', 'string'],
            'fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'vehicle_category_id' => 'required|string|exists:vehicle_categories,id',
            'transmission_type_id' =>'nullable|string|exists:car_transmission_types,id',
            'construction_year' => ['nullable', 'integer'],
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'length_cm' => ['nullable', 'numeric'],
            'width_cm' => ['nullable', 'numeric'],
            'height_cm' => ['nullable', 'numeric'],
            'max_weight_allowed_kg' => ['nullable', 'numeric'],
            'payload_kg' => ['nullable', 'numeric'],
            'engine_displacement' => ['nullable', 'integer'],
            'mileage' => ['required', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'axes' => ['nullable', 'integer'],
            'seats' => ['nullable', 'integer'],
            'sleeping_places' => ['nullable', 'integer'],
            'beds' => ['nullable', 'string'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'color' => ['nullable', 'string'],
            'price' => ['required', 'numeric','max:99999999'],
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
            'doors' => ['nullable', 'integer'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'drive_type_id' => 'nullable|string|exists:car_wheel_drive_types,id',
            'generation_id' => 'nullable|string',
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
            
            $ad =  Ad::where('id',$id)->first();
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
            Redis::del('by_user_'.Auth::user()->id.'_filter_auto');

            $mobileHomeAd = MobileHomeAd::where('ad_id',$id)->first();
            $mobileHomeAd->make_id = $request['make_id'];
            $mobileHomeAd->custom_make = $request['custom_make'];

            $mobileHomeAd->price_contains_vat = $request['price_contains_vat'];
            $mobileHomeAd->model_id = $request['model_id'];
            $mobileHomeAd->custom_model = $request['model'];
            $mobileHomeAd->fuel_type_id = $request['fuel_type_id'];
            $mobileHomeAd->vehicle_category_id = $request['vehicle_category_id'];
            $mobileHomeAd->transmission_type_id = $request['transmission_type_id'];
            $mobileHomeAd->construction_year = $request['construction_year'];
            $mobileHomeAd->first_registration_month = $request['first_registration_month'];
            $mobileHomeAd->first_registration_year = $request['first_registration_year'];
            $mobileHomeAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $mobileHomeAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $mobileHomeAd->owners = $request['owners'];
            $mobileHomeAd->length_cm = $request['length_cm'];
            $mobileHomeAd->width_cm = $request['width_cm'];
            $mobileHomeAd->height_cm = $request['height_cm'];
            $mobileHomeAd->max_weight_allowed_kg = $request['max_weight_allowed_kg'];
            $mobileHomeAd->engine_displacement = $request['engine_displacement'];
            $mobileHomeAd->mileage = $request['mileage'];
            $mobileHomeAd->power_kw = $request['power_hp'];
            $mobileHomeAd->axes = $request['axes'];
            $mobileHomeAd->seats = $request['seats'];
            $mobileHomeAd->sleeping_places = $request['sleeping_places'];
            $mobileHomeAd->beds = $request['beds'];
            $mobileHomeAd->emission_class = $request['emission_class'];
            $mobileHomeAd->fuel_consumption = $request['fuel_consumption'];
            $mobileHomeAd->co2_emissions = $request['co2_emissions'];
            $mobileHomeAd->condition = $request['condition'];
            $mobileHomeAd->color = $request['color'];
            $mobileHomeAd->price = $request['price'];
            $mobileHomeAd->first_name = $request['first_name'];
            $mobileHomeAd->last_name = $request['last_name'];
            $mobileHomeAd->email_address = $request['email_address'];
            $mobileHomeAd->address = $request['address'];
            $mobileHomeAd->zip_code = $request['zip_code'];
            $mobileHomeAd->city = $request['city'];
            $mobileHomeAd->country = $request['country'];
            $mobileHomeAd->zip_code = $request['zip_code'];
            $mobileHomeAd->mobile_number = $request['mobile_number'];
            $mobileHomeAd->landline_number = $request['landline_number'];
            $mobileHomeAd->whatsapp_number = $request['whatsapp_number'];
            $mobileHomeAd->youtube_link = $request['youtube_link'];
            $mobileHomeAd->doors = $request['doors'];
            $mobileHomeAd->additional_vehicle_info = $request['additional_vehicle_info'];
            $mobileHomeAd->drive_type_id = $request['drive_type_id'];
            $mobileHomeAd->generation = $request['generation_id'];
            $mobileHomeAd->save();
            
            $ad_sub_characteristics = [];

            AdSubCharacteristic::where('ad_id',$id)->delete();

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
                    'mobile_home_ad' =>  $mobileHomeAd, 
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
     * @param DestroyMobileHomeAd $request
     * @param MobileHomeAd $mobile_home_ad
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMobileHomeAd $request, MobileHomeAd $mobile_home_ad)
    {
        $mobile_home_ad->delete();
        Redis::del('by_user_'.Auth::user()->id.'_filter_auto');

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMobileHomeAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMobileHomeAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MobileHomeAd::whereIn('id', $bulkChunk)->delete();

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

    public function mobileHomeAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','mobile-home')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'mobileHomeAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                        },
                    ]
                );

        return ['data' => $data->get()];
    }
}
