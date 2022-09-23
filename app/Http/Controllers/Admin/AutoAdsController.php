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
use Illuminate\Support\Facades\Redis as Redis;

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
        // dd(Redis::exists('auto_ads') && !$request->filters && $request->query->get('orderBy') == null?"true":"false");
        // dd(!$request->filters && $request->query->get('orderBy') == 'created_at' && $request->query->get('orderDirection') == 'desc');
        if(
            Redis::exists('auto_ads_ult') && 
            !$request->filters &&
            $request->query->get('orderBy') == 'created_at' &&
            $request->query->get('orderDirection') == 'desc'
        ) {
            $data = json_decode(Redis::get('auto_ads_ult'));
            return ['data' => $data,'redis'=>'true'];
        }

        if(
            Redis::exists('auto_ads') && 
            !$request->filters &&
            $request->query->get('orderBy') == null
        ) {
            $data = json_decode(Redis::get('auto_ads'));
            return ['data' => $data,'redis'=>'true'];
        }
        
        $promoted_simple_ads = AutoAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)')->inRandomOrder()->limit(25);
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

                if(Redis::exists('auto_ads_relation')) {
                    $relation = json_decode(Redis::get('auto_ads_relation'));
        
                }else{
                    $relation = AutoAd::getRelationships();
                    Redis::set('auto_ads_relation',json_encode($relation ));
                }
                foreach ($relation as $key => $value) {
                   $query->with($key);
                }
                
                $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');
                
                $query->with([
                    'ad' => function($query)
                    {
                        $query->with(['images','user']);
                    }
                ]);
            }
        );
        $data = $data->toArray(); 
            
        array_push($promoted,...$data['data']);
    
        $data['data'] = $promoted;

        if(
            !$request->filters &&
            $request->query->get('orderBy') == 'created_at' &&
            $request->query->get('orderDirection') == 'desc'
        ){
            Redis::set('auto_ads_ult',json_encode($data));
            return ['data' => $data];
        }
        if(
            !$request->filters &&
            $request->query->get('orderBy') == null
        ) {
            Redis::set('auto_ads',json_encode($data));
            return ['data' => $data, "redis"=>'false'];
        }

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

        
        $data = AutoAd::whereRaw("ad_id in (SELECT id FROM ads where (ads.title LIKE '%".$filter."%' or ads.description LIKE '%".$filter."%') and type = 'auto')")
                    ->with(['make',
                            'model',
                            'ad'=> function($query)
                            {
                                $query->with(['images']);
                            },
                            'generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])->paginate(25);

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
        $this->authorize('admin.auto-ad.create');

        return view('admin.auto-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAutoAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
   

        $validator = Validator::make($request->all(), [
            'make_id' => 'required|string|exists:makes,id',
            'model_id' => 'required|string|exists:models,id',
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'generation_id' => 'nullable|string|exists:generations,id',
            'mileage' => ['required', 'integer'],
            'condition' => ['required', 'string'],
            'exterior_color' => ['required', 'string'],
            'interior_color' => ['nullable', 'string'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'ad_fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'ad_transmission_type_id' => 'nullable|string|exists:car_transmission_types,id',
            'ad_drive_type_id' => 'nullable|string|exists:car_wheel_drive_types,id',
            'ad_body_type_id' => 'nullable|string|exists:car_body_types,id',
            'engine_displacement' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'sub_characteristic_ids' => ['required', 'array'],
            'doors' => ['nullable', 'integer'],
            'seats' => ['nullable', 'integer'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'market_id' => 'required|string|exists:markets,id',
            'youtube_link' => ['nullable', 'string'],
            'price' => ['required', 'numeric','max:99999999'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email_address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'address' => ['required', 'string'],
            'mobile_number' => ['required', 'string'],
            'whatsapp_number' => ['required', 'string']
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
            $ad->description = $request['description'];
            $ad->status =  0;
            $ad->type =  'auto';
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
            Redis::del('auto_ads');
            Redis::del('auto_ads_ult');
            Redis::del('by_user_'.Auth::user()->id.'_filter_auto');
            
            $autoAd = new AutoAd;
            $autoAd->ad_id = $ad->id;
            $autoAd->price = $request['price'];
            $autoAd->price_contains_vat = 0;
            $autoAd->doors = $request['doors'];
            $autoAd->mileage = $request['mileage'];
            $autoAd->exterior_color = $request['exterior_color'];
            $autoAd->interior_color = $request['interior_color'];
            $autoAd->condition = $request['condition'] ;
            $autoAd->dealer_id =  Auth::user()->dealer_id ?? null;
            $autoAd->dealer_show_room_id = $dealer_show_room_id;
            $autoAd->first_name =  $request['first_name'];
            $autoAd->last_name =$request['last_name'] ;
            $autoAd->email_address = $request['email_address'];
            $autoAd->address = $request['address'];
            $autoAd->zip_code = $request['zip_code'];
            $autoAd->city = $request['city'];
            $autoAd->country = $request['country'];
            $autoAd->mobile_number = $request['mobile_number'];
            $autoAd->landline_number = $request['landline_number'];
            $autoAd->whatsapp_number = $request['whatsapp_number'];
            $autoAd->youtube_link = $request['youtube_link'];
            $autoAd->ad_fuel_type_id = $request['ad_fuel_type_id'];
            $autoAd->ad_body_type_id = $request['ad_body_type_id'];
            $autoAd->ad_transmission_type_id = $request['ad_transmission_type_id'];
            $autoAd->ad_drive_type_id = $request['ad_drive_type_id'];
            $autoAd->first_registration_month = $request['first_registration_month'];
            $autoAd->first_registration_year = $request['first_registration_year'];
            $autoAd->engine_displacement = $request['engine_displacement'];
            $autoAd->power_hp = $request['power_hp'];
            $autoAd->owners = $request['owners'];
            $autoAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $autoAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $autoAd->make_id = $request['make_id'];
            $autoAd->model_id = $request['model_id'];
            $autoAd->generation_id = $request['generation_id'];
            $autoAd->series_id = $request['series_id'];
            $autoAd->additional_vehicle_info = $request['additional_vehicle_info'];
            $autoAd->seats = $request['seats'];
            $autoAd->fuel_consumption = $request['fuel_consumption'];
            $autoAd->co2_emissions = $request['co2_emissions'];
            $autoAd->latitude = $request['latitude'];
            $autoAd->longitude = $request['longitude'];
            $autoAd->geocoding_status = $request['geocoding_status'];
            $autoAd->save();
            
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
                    'auto_ad' =>  $autoAd, 
                    'ad_sub_characteristics' => $ad_sub_characteristics
                ]
            ], 200);

        } catch (Exception $e) {
            $ad->delete();
            ApiHelper::setError($resource, 0, 500, $e->getMessage().', Line: '.$e->getLine());
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
    public function update(Request $request,$id)
    {
        
        $validator = Validator::make($request->all(), [
            'make_id' => 'required|string|exists:makes,id',
            'model_id' => 'required|string|exists:models,id',
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'generation_id' =>  'nullable|string|exists:generations,id',
            'mileage' => ['required', 'integer'],
            'condition' => ['required', 'string'],
            'exterior_color' => ['required', 'string'],
            'interior_color' => ['nullable', 'string'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'ad_fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'ad_transmission_type_id' => 'nullable|string|exists:car_transmission_types,id',
            'ad_drive_type_id' => 'nullable|string|exists:car_wheel_drive_types,id',
            'ad_body_type_id' => 'nullable|string|exists:car_body_types,id',
            'engine_displacement' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'sub_characteristic_ids' => ['required', 'array'],
            'doors' => ['nullable', 'integer'],
            'seats' => ['nullable', 'integer'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'market_id' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'price' => ['required', 'numeric','max:99999999'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email_address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'address' => ['required', 'string'],
            'mobile_number' => ['required', 'string'],
            'whatsapp_number' => ['required', 'string'],
            'image_ids' => ['nullable', 'array'],
            'eliminated_thumbnail' => ['required', 'boolean']
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

            Redis::del('auto_ads');
            Redis::del('auto_ads_ult');
            Redis::del('by_user_'.Auth::user()->id.'_filter_auto');

            $autoAd = AutoAd::where('ad_id',$id)->first();
            $autoAd->price = $request['price'];
            $autoAd->doors = $request['doors'];
            $autoAd->mileage = $request['mileage'];
            $autoAd->exterior_color = $request['exterior_color'];
            $autoAd->interior_color = $request['interior_color'];
            $autoAd->condition = $request['condition'] ;
            $autoAd->first_name =  $request['first_name'];
            $autoAd->last_name =$request['last_name'] ;
            $autoAd->email_address = $request['email_address'];
            $autoAd->address = $request['address'];
            $autoAd->zip_code = $request['zip_code'];
            $autoAd->city = $request['city'];
            $autoAd->country = $request['country'];
            $autoAd->mobile_number = $request['mobile_number'];
            $autoAd->landline_number = $request['landline_number'];
            $autoAd->whatsapp_number = $request['whatsapp_number'];
            $autoAd->youtube_link = $request['youtube_link'];
            $autoAd->ad_fuel_type_id = $request['ad_fuel_type_id'];
            $autoAd->ad_body_type_id = $request['ad_body_type_id'];
            $autoAd->ad_transmission_type_id = $request['ad_transmission_type_id'];
            $autoAd->ad_drive_type_id = $request['ad_drive_type_id'];
            $autoAd->first_registration_month = $request['first_registration_month'];
            $autoAd->first_registration_year = $request['first_registration_year'];
            $autoAd->engine_displacement = $request['engine_displacement'];
            $autoAd->power_hp = $request['power_hp'];
            $autoAd->owners = $request['owners'];
            $autoAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $autoAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $autoAd->make_id = $request['make_id'];
            $autoAd->model_id = $request['model_id'];
            $autoAd->generation_id = $request['generation_id'];
            $autoAd->series_id = $request['series_id'];
            $autoAd->additional_vehicle_info = $request['additional_vehicle_info'];
            $autoAd->seats = $request['seats'];
            $autoAd->fuel_consumption = $request['fuel_consumption'];
            $autoAd->co2_emissions = $request['co2_emissions'];
            $autoAd->latitude = $request['latitude'];
            $autoAd->longitude = $request['longitude'];
            $autoAd->geocoding_status = $request['geocoding_status'];
            $autoAd->save();
            
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
                    'auto_ad' =>  $autoAd, 
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
     * @param DestroyAutoAd $request
     * @param AutoAd $autoAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAutoAd $request, AutoAd $autoAd)
    {
        $autoAd->delete();
        Redis::del('auto_ads');
        Redis::del('auto_ads_ult');
        Redis::del('by_user_'.Auth::user()->id.'_filter_auto');
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
