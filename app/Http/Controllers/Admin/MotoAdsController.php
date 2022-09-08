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
        
        $promoted_simple_ads = MotoAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)')->inRandomOrder()->limit(25);

        foreach (MotoAd::getRelationships() as $key => $value) {
           $promoted_simple_ads->with($key);
        }

        $promoted = $promoted_simple_ads->get()->toArray();

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

                $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');
                
                foreach (MotoAd::getRelationships() as $key => $value) {
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

        $data = MotoAd::whereRaw("ad_id in (SELECT id FROM ads where (ads.title LIKE '%".$filter."%' or ads.description LIKE '%".$filter."%') and type = 'moto')")
                    ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images']);
                    } ,
                    'fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])->paginate(25);

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
        $this->authorize('admin.moto-ad.create');

        return view('admin.moto-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMotoAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'market_id' => 'required|string|exists:markets,id',
            'make_id' => 'required|string|exists:makes,id',
            'custom_make' => ['nullable', 'string'],
            'model_id' => 'nullable|string|exists:models,id',
            'custom_model' => ['nullable', 'string'],
            'fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'body_type_id' => 'nullable|string|exists:car_body_types,id',
            'transmission_type_id' =>'nullable|string|exists:car_transmission_types,id',
            'drive_type_id' => 'nullable|string|exists:car_wheel_drive_types,id',
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'last_customer_service_month' => ['nullable', 'integer'],
            'last_customer_service_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'weight_kg' => ['nullable', 'numeric'],
            'engine_displacement' => ['nullable', 'integer'],
            'mileage' => ['required', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'gears' => ['nullable', 'integer'],
            'cylinders' => ['nullable', 'integer'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'color' => ['required', 'string'],
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
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        if (count($request->file()) > 3) {
            ApiHelper::setError($resource, 0, 422,['files' => 'Has excedido el numero maximos de imagenes']);
            return $this->sendResponse($resource);
        }

        if (count($request->file())  == 0) {
            ApiHelper::setError($resource, 0, 422,['files' => 'Debe enviar minimo 1 imagen para publicar']);
            return $this->sendResponse($resource);
        }

        try {
            
            $slug = $this->slugAd($request['title']);

            $ad = new Ad;
            $ad->slug =  $slug;
            $ad->title =  $request['title'];
            $ad->description =  $request['description'];
            $ad->status =  0;
            $ad->type =  'moto';
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

            $motoAd = new MotoAd;
            $motoAd->ad_id = $ad->id;
            $motoAd->price = $request['price'];
            $motoAd->price_contains_vat = 0;
            $motoAd->mileage = $request['mileage'];
            $motoAd->color = $request['color'];
            $motoAd->condition = $request['condition'] ;
            $motoAd->dealer_id =  Auth::user()->dealer_id ?? null;
            $motoAd->dealer_show_room_id = $dealer_show_room_id;
            $motoAd->first_name =  $request['first_name'];
            $motoAd->last_name =$request['last_name'] ;
            $motoAd->email_address = $request['email_address'];
            $motoAd->address = $request['address'];
            $motoAd->zip_code = $request['zip_code'];
            $motoAd->city = $request['city'];
            $motoAd->country = $request['country'];
            $motoAd->mobile_number = $request['mobile_number'];
            $motoAd->landline_number = $request['landline_number'];
            $motoAd->whatsapp_number = $request['whatsapp_number'];
            $motoAd->youtube_link = $request['youtube_link'];
            $motoAd->fuel_type_id = $request['fuel_type_id'];
            $motoAd->body_type_id = $request['body_type_id'];
            $motoAd->transmission_type_id = $request['transmission_type_id'];
            $motoAd->drive_type_id = $request['drive_type_id'];
            $motoAd->first_registration_month = $request['first_registration_month'];
            $motoAd->first_registration_year = $request['first_registration_year'];
            $motoAd->engine_displacement = $request['engine_displacement'];
            $motoAd->power_kw = $request['power_hp'];
            $motoAd->owners = $request['owners'];
            $motoAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $motoAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $motoAd->make_id = $request['make_id'];
            $motoAd->model_id = $request['model_id'];
            $motoAd->fuel_consumption = $request['fuel_consumption'];
            $motoAd->co2_emissions = $request['co2_emissions'];
            $motoAd->save();
            
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
                    'moto_ad' =>  $motoAd, 
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
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'market_id' => ['required', 'string'],
            'make_id' => ['nullable', 'string'],
            'custom_make' => ['nullable', 'string'],
            'model_id' => ['nullable', 'string'],
            'custom_model' => ['nullable', 'string'],
            'fuel_type_id' => 'required|string|exists:car_fuel_types,id',
            'body_type_id' => 'nullable|string|exists:car_body_types,id',
            'transmission_type_id' =>'nullable|string|exists:car_transmission_types,id',
            'drive_type_id' => ['nullable', 'string'],
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'last_customer_service_month' => ['nullable', 'integer'],
            'last_customer_service_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'weight_kg' => ['nullable', 'numeric'],
            'engine_displacement' => ['nullable', 'integer'],
            'mileage' => ['required', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'gears' => ['nullable', 'integer'],
            'cylinders' => ['nullable', 'integer'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'color' => ['required', 'string'],
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
            'eliminated_thumbnail' => ['required', 'boolean']
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        if (count($request->file()) > 3) {
            ApiHelper::setError($resource, 0, 422,['files' => 'Has excedido el numero maximos de imagenes']);
            return $this->sendResponse($resource);
        }

        if (count($request->file())  == 0) {
            ApiHelper::setError($resource, 0, 422,['files' => 'Debe enviar minimo 1 imagen para publicar']);
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

            $motoAd = MotoAd::where('ad_id',$id)->first();
            $motoAd->price = $request['price'];
            $motoAd->mileage = $request['mileage'];
            $motoAd->color = $request['color'];
            $motoAd->condition = $request['condition'] ;
            $motoAd->first_name =  $request['first_name'];
            $motoAd->last_name =$request['last_name'] ;
            $motoAd->email_address = $request['email_address'];
            $motoAd->address = $request['address'];
            $motoAd->zip_code = $request['zip_code'];
            $motoAd->city = $request['city'];
            $motoAd->country = $request['country'];
            $motoAd->mobile_number = $request['mobile_number'];
            $motoAd->landline_number = $request['landline_number'];
            $motoAd->whatsapp_number = $request['whatsapp_number'];
            $motoAd->youtube_link = $request['youtube_link'];
            $motoAd->fuel_type_id = $request['fuel_type_id'];
            $motoAd->body_type_id = $request['body_type_id'];
            $motoAd->transmission_type_id = $request['transmission_type_id'];
            $motoAd->drive_type_id = $request['drive_type_id'];
            $motoAd->first_registration_month = $request['first_registration_month'];
            $motoAd->first_registration_year = $request['first_registration_year'];
            $motoAd->engine_displacement = $request['engine_displacement'];
            $motoAd->power_kw = $request['power_hp'];
            $motoAd->owners = $request['owners'];
            $motoAd->inspection_valid_until_month = $request['inspection_valid_until_month'];
            $motoAd->inspection_valid_until_year = $request['inspection_valid_until_year'];
            $motoAd->make_id = $request['make_id'];
            $motoAd->model_id = $request['model_id'];
            $motoAd->fuel_consumption = $request['fuel_consumption'];
            $motoAd->co2_emissions = $request['co2_emissions'];
            $motoAd->save();
            
            $ad_sub_characteristics = [];

            AdSubCharacteristic::where('ad_id',$id)->delete();

            foreach ($request->sub_characteristic_ids as  $sub_characteristic_id) {
                $ad_sub_characteristic =  new AdSubCharacteristic;
                $ad_sub_characteristic->ad_id = $ad->id;
                $ad_sub_characteristic->sub_characteristic_id = $sub_characteristic_id;
                $ad_sub_characteristic->save();
                array_push($ad_sub_characteristics, $ad_sub_characteristic);
            }


         //   $user = Auth::user();

           // $user->notify(new \App\Notifications\NewAd($user));
            
            return response()->json([
                'data' => [
                    'ad' => $ad,
                    'moto_ad' =>  $motoAd, 
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

     public function motoAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','moto')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'motoAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        }
                    ]
                );

        return ['data' => $data->get()];
    }
}
