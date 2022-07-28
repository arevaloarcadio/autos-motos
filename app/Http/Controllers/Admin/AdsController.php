<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\{NotifyRejected,NotifyApproved};
use App\Http\Requests\Admin\Ad\BulkDestroyAd;
use App\Http\Requests\Admin\Ad\DestroyAd;
use App\Http\Requests\Admin\Ad\IndexAd;
use App\Http\Requests\Admin\Ad\StoreAd;
use App\Http\Requests\Admin\Ad\UpdateAd;
use App\Models\{Ad,CsvAd,RejectedComment,AdRejectedComment,User,AutoAd,MotoAd,MechanicAd,MobileHomeAd,ShopAd,TruckAd,RentalAd};
use Brackets\AdminListing\Facades\AdminListing;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AdsController extends Controller
{
      use ApiController;

    /**
     * Display a listing of the resource.
     *
     * @param IndexAd $request
     * @return array|Factory|View
     */
    public function index(IndexAd $request)
    {   
        $promoted = [];

        $promoted_simple_ads = Ad::whereRaw('id in(SELECT ad_id FROM promoted_simple_ads)')->inRandomOrder()->limit(25);

        $promoted_simple_ads->with([
                    'mechanicAd',
                    'rentalAd',
                    'images',
                    'autoAd' => function($query)
                    {
                        $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'motoAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'mobileHomeAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]);

        $promoted = $promoted_simple_ads->get()->toArray();
        
        $data = AdminListing::create(Ad::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'thumbnail', 'status', 'type',  'status','is_featured', 'user_id', 'market_id', 'external_id', 'source', 'images_processing_status','csv_ad_id'],

            // set columns to searchIn
            ['id', 'slug', 'title', 'description', 'thumbnail', 'status', 'type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','csv_ad_id','created_at'],

            function ($query) use ($request) {
                     
                $columns =  ['id', 'slug', 'title', 'description', 'status', 'thumbnail', 'type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','csv_ad_id','created_at'];

               
                if ($request->filters) {
                    foreach ($columns as $column) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                                if ($key == 'created_at') {
                                    $query->where($key,'LIKE','%'.$filter.'%');
                                }else{
                                    $query->where($key,$filter);
                                }
                            }
                        }
                    }
                }
                
                $where_ad_id = null;

                if ($request->input('types')) {
                    $query->whereIn('type',$request->input('types'));
                }
               
                $i = 1;
                $type_ads = [
                    'auto_ads',
                    'moto_ads',
                    'mechanic_ads',
                    'mobile_home_ads',
                    'rental_ads',
                    'shop_ads',
                    'truck_ads' 
                ];

                foreach ($type_ads as $type) {
                    if ($i == 1) {
                        $where_ad_id .= sprintf('(ads.id in (SELECT ad_id from %s) ',$type);
                    }else if ($i == count($type_ads)) {
                        $where_ad_id .= sprintf(' or ads.id in (SELECT ad_id from %s)) ',$type);
                    }else{
                        $where_ad_id .= sprintf(' or ads.id in (SELECT ad_id from %s) ',$type);
                    }
                    $i++; 
                }
                  
                $query->whereRaw($where_ad_id); 
                
                $query->with(
                    [
                        'mechanicAd',
                        'rentalAd',
                        'images',
                        'autoAd' => function($query)
                        {
                            $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        },
                        'motoAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        },
                        'mobileHomeAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                        },
                        'truckAd' => function($query)
                        {
                            $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                        },
                        'shopAd' => function($query)
                        {
                            $query->with(['make','model','ad','dealer','dealerShowRoom']);
                        }
                    ]
                );
            }
        );

        $data = $data->toArray(); 
            
        array_push($promoted,...$data['data']);
    
        $data['data'] = $promoted;
      
        return ['data' => $data];
    }


   

    public function searchAdsLike(Request $request)
    {   
        $validator = \Validator::make($request->all(), [
            'filter' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => json_decode($validator->errors()->toJson())],422);
        }

        $filter = $request->filter;
        
        $ads = Ad::where(function ($query) use ($filter){
                $query->where('ads.title','LIKE','%'. $filter.'%')
                      ->orWhere('ads.description','LIKE','%'.$filter.'%')
                      ->orWhere('ads.type','LIKE','%'.$filter.'%');
            })->limit(50);

        $ads->with(
                    [
                        'mechanicAd',
                        'rentalAd',
                        'images',
                        'autoAd' => function($query)
                        {
                            $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        },
                        'motoAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        },
                        'mobileHomeAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                        },
                        'truckAd' => function($query)
                        {
                            $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                        },
                        'shopAd' => function($query)
                        {
                            $query->with(['make','model','ad','dealer','dealerShowRoom']);
                        }
                    ]
                );

        return response()->json([
            'data' => $ads->get(),
            'total' => count($ads->get()) 
        ]);
    }

    public function bySource(Request $request)
    {

        $ads = Ad::selectRaw('count(*) as count,source')
            ->where('source','!=',NULL)
            ->where('source','!=','CSV')
            ->groupBy('source');
        
        if ($request->dateStart && $request->dateEnd) {
             $ads->whereBetween('created_at',[$request->dateStart,$request->dateEnd]);
        }

        return ['data' => $ads->get()];
    }


    public function byUser(Request $request)
    {
        $data = Ad::where('user_id',Auth::user()->id)
                    ->orderBy('created_at','DESC')
                    ->limit(20);
        
        if ($request->filter) {
            if ($request->filter == 'autos') {
                $data = $data->whereIn('type',['auto','mobile-home','moto','truck']);
            }else{
                $data = $data->where('type',$request->filter);
            }
        } 

        $data->with(
                    [
                        'mechanicAd',
                        'rentalAd',
                        'images',
                        'autoAd' => function($query)
                        {
                            $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        },
                        'motoAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                        },
                        'mobileHomeAd' => function($query)
                        {
                            $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                        },
                        'truckAd' => function($query)
                        {
                            $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                        },
                        'shopAd' => function($query)
                        {
                            $query->with(['make','model','ad','dealer','dealerShowRoom']);
                        }
                    ]
                );
      
        return ['data' => $data->get()];
    }

    public function byCsv(Request $request,$csv_ad_id)
    {
        $ads = Ad::where('source','CSV')
                ->where('csv_ad_id',$csv_ad_id)
                ->where('status',0)
                ->with(['csv_ad','autoAd' => function($query)
                    {
                        $query->with(['make','model']);
                    }
                ])
                ->get();
        
        return ['data' => $ads];
    }

    public function groupByCsv(Request $request)
    {
        $ads = CsvAd::select('csv_ads.*')
            ->with('user');
        
        if ($request->type) {
            $ads = $ads->join('users','users.id','csv_ads.user_id')
                        ->where('users.type',$request->type);
        }
        
        if ($request->date) {
            $ads->where('csv_ads.created_at','LIKE','%'.$request->date.'%');
        }

        if ($request->sort) {
            $ads->where('csv_ads.status',$request->sort);
        }

        return ['data' => $ads->paginate(25)];
    }

    public function countAdsToday(Request $request)
    {
        $today = date('Y-m-d');
        $count_ads = Ad::where('created_at','LIKE','%'.$today.'%')->count();
        
        return ['data' => $count_ads];
    }

     public function countAdsImportToday(Request $request)
    {
        
        $count_ads = Ad::where(function($query){
            
            $today = date('Y-m-d');
            
            $sources = [
                'INVENTARIO_IMPORT',
                'MECHANICS_IMPORT',
                'PORTAL',
                'PORTAL_CLUB_IMPORT',
                'RENTALS_IMPORT',
                'WEB_MOBILE_24'
            ];

            $query->where('created_at','LIKE','%'.$today.'%')->whereIn('source',$sources);
        })->count();

        return ['data' => $count_ads];
    }


    public function setApprovedRejected(Request $request,$status)
    {   
        $resource = ApiHelper::resource();
       
        $validator = Validator::make($request->all(), [
            'ad_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }
        
        try {
            
            Ad::whereIn('id',$request->ad_ids)
                ->update(
                    [
                        'status' => $status == 'approved' ? 10 : 20
                    ]
                );

            $ads = Ad::select('ads.user_id','ads.title')
                ->whereIn('id',$request->ad_ids)
                ->groupBy('ads.user_id','ads.title')
                ->get();
            
            foreach ($ads as $ad) {
                $user = User::find($ad->user_id);
                $status == 'approved' ? $user->notify(new NotifyApproved($ad->title)) :  $user->notify(new NotifyRejected($ad->title));
            }
               
            return response()->json(['data' => 'OK'], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

     public function setApprovedRejectedIndividual(Request $request,$status)
    {   
        
        $resource = ApiHelper::resource();
       
        $validator = Validator::make($request->all(), [
            'ads' => 'required|array',
            'ads.*.ad_id' => 'required',
            'ads.*.user_id' => 'required',
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }

        foreach ($request->ads as $ad) {
            Ad::where('id',$ad['ad_id'])
                ->update(
                    [
                        'status' => $status == 'approved' ? 10 : 20
                    ]
                );
            $ad_ =  Ad::find($ad['ad_id']);

            $user = User::find($ad['user_id']);

            $status == 'approved' ? $user->notify(new NotifyApproved($ad_->title)) :  $user->notify(new NotifyRejected($ad_->title));
        }

        

        return ['data' => 'OK'];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.ad.create');

        return view('admin.ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['status'] = 0;
        $sanitized['slug'] = Str::slug($sanitized['title']);
        // Store the Ad
        $ad = Ad::create($sanitized);

        return ['data' => $ad];
    }

    /**
     * Display the specified resource.
     *
     * @param Ad $ad
     * @throws AuthorizationException
     * @return void
     */
    public function show($id)
    {   
        $ad = Ad::find($id);

        $ad->with([
                    'mechanicAd',
                    'rentalAd',
                    'images',
                    'autoAd' => function($query)
                    {
                        $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'motoAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'mobileHomeAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]);

        return ['data' => $ad];
    }


    public function storeCommentRejected(Request $request,$ad)
    {   
        $ad = Ad::find($ad);
        
        $rejected_comment = new RejectedComment;
        $rejected_comment->comment = $request->comment;
        $rejected_comment->save();

        $ad_rejected_comment = new AdRejectedComment;
        $ad_rejected_comment->ad_id = $ad->id;
        $ad_rejected_comment->rejected_comment_id = $rejected_comment->id;
        $ad_rejected_comment->save();
        
        return ['data' => 'OK'];
    }

     public function storeCommentsRejected(Request $request,$csv_ad_id)
    {   
        $rejected_comment = new RejectedComment;
        $rejected_comment->comment = $request->comment;
        $rejected_comment->save();

        $ads = Ad::where('csv_ad_id',$csv_ad_id)->get();

        foreach ($ads as $ad) {
            $ad_rejected_comment = new AdRejectedComment;
            $ad_rejected_comment->ad_id = $ad->id;
            $ad_rejected_comment->rejected_comment_id = $rejected_comment->id;
            $ad_rejected_comment->save();
        }
        
        return ['data' => 'OK'];
    }

    public function storeCommentsRejectedIndividual(Request $request)
    {   

        $validator = \Validator::make($request->all(), [
            'ads' => 'required|array',
            'comment' => 'required|string',
        ]);

        $rejected_comment = new RejectedComment;
        $rejected_comment->comment = $request->comment;
        $rejected_comment->save();

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors()],422);
        }
        
        foreach ($request->ads as $ad) {
            $ad_rejected_comment = new AdRejectedComment;
            $ad_rejected_comment->ad_id = $ad;
            $ad_rejected_comment->rejected_comment_id = $rejected_comment->id;
            $ad_rejected_comment->save();
        }

        return ['data' => 'OK'];
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param Ad $ad
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Ad $ad)
    {
        $this->authorize('admin.ad.edit', $ad);


        return view('admin.ad.edit', [
            'ad' => $ad,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAd $request
     * @param Ad $ad
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAd $request, Ad $ad)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Ad
        $ad->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAd $request
     * @param Ad $ad
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAd $request, Ad $ad)
    {   


        $resource = ApiHelper::resource();
        
        try {

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            switch ($ad->type) {
                case 'auto':
                    AutoAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'moto':
                    MotoAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'mobile-home':
                    MobileHomeAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'truck':
                    TruckAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'rental':
                    RentalAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'shop':
                    ShopAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'mechanic':
                    MechanicAd::where('ad_id',$ad->id)->delete();
                    break;
                default:
                    AutoAd::where('ad_id',$ad->id)->delete();
                    MotoAd::where('ad_id',$ad->id)->delete();
                    MobileHomeAd::where('ad_id',$ad->id)->delete();
                    TruckAd::where('ad_id',$ad->id)->delete();
                    RentalAd::where('ad_id',$ad->id)->delete();
                    ShopAd::where('ad_id',$ad->id)->delete();
                    MechanicAd::where('ad_id',$ad->id)->delete();
                    break;
            }
          
            $ad->delete();
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return response()->json(['data' => 'OK'], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Ad::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function getTypeAds($key){

        $type_ads = [
            'auto' => 'auto_ads',
            'moto' => 'moto_ads',
            'mechanic' => 'mechanic_ads',
            'mobile-home' => 'mobile_home_ads',
            'rental' => 'rental_ads',
            'shop' => 'shop_ads',
            'truck' => 'truck_ads'
        ];
        
        if (isset($type_ads[$key])) {
            return $type_ads[$key];
        }

        return null;
    }

    public function searchAdvanced(Request $request)
    {   
        
        $resource = ApiHelper::resource();
        $filter_types = [];
        $response = [];
      
        try {
            
            array_push($response, ...$this->getPromotedAds($request->types));
          
            if ($request->types) {
                foreach ($request->types as $type) {
                    switch ($type) {
                        case 'auto':
                            array_push($response, ...$this->getAutoAd($request));
                            break;
                        case 'moto':
                            array_push($response,...$this->getMotoAd($request));
                            break;
                        case 'mobile-home':
                            array_push($response, ...$this->getMobileHomeAd($request));
                            break;
                        case 'truck':
                            array_push($response, ...$this->getTruckAd($request));
                            break;
                        default:
                            
                            break;
                    }
                }
            }
               
            return response()->json(['count' => count($response),'data' => $response], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


    public function getPromotedAds($types)
    {   
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_simple_ads)')->whereIn('type',$types)->inRandomOrder()->limit(10);

        $data->with([
                    'mechanicAd',
                    'rentalAd',
                    'images',
                    'autoAd' => function($query)
                    {
                        $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'motoAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'mobileHomeAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]);

        return $data->get()->toArray();
    }

    public function getAutoAd($filters)
    {
        $auto_ad = new AutoAd;
         
        $auto_ad = $auto_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }

            if ($filters->model_id) {
                $query->orWhere('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }
            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->doors) {
                $query->orWhere('doors',$filters->doors);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('ad_fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('ad_transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->orWhere('ad_drive_type_id',$filters->drive_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_hp && $filters->to_power_hp){
                $query->orWhereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->from_engine_displacement && $filters->to_engine_displacement){
                $query->orWhereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->exterior_color) {
                $query->orWhere('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->orWhere('interior_color',$filters->interior_color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->seats) {
                $query->orWhere('seats',$filters->seats);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });
        if ($filters->oldest) {
            $auto_ad->orderBy('created_at','DESC');
        }
        
        if ($filters->newer) {
            $auto_ad->orderBy('created_at','ASC');
        }

        if ($filters->higher_price) {
            $auto_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $auto_ad->orderBy('price','ASC');
        }

        if (isset($filters->page)) {
            $offset = $filters->page * 25;
            $auto_ad = $auto_ad->offset($offset);    
        }

        
        return $auto_ad
            ->with(['make',
                    'model',
                    'ad'=> function($query)
                    {
                        $query->with(['images']);
                    },'generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])
            ->limit(25)
            ->get()
            ->toArray();
    }

    public function getMotoAd($filters)
    {
        $moto_ad = new MotoAd;

        $moto_ad = $moto_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->orWhere('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }
            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->orWhere('drive_type_id',$filters->drive_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_hp && $filters->to_power_hp){
                $query->orWhereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->from_engine_displacement && $filters->to_engine_displacement){
                $query->orWhereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->orWhere('color',$filters->color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->cylinders) {
                $query->orWhere('cylinders',$filters->seats);
            }
            if ($filters->gears) {
                $query->orWhere('gears',$filters->gears);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });
        
        if ($filters->oldest) {
            $moto_ad->orderBy('created_at','DESC');
        }
        
        if ($filters->newer) {
            $moto_ad->orderBy('created_at','ASC');
        }

        if ($filters->higher_price) {
            $moto_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $moto_ad->orderBy('price','ASC');
        }

        if (isset($filters->page)) {
            $offset = $filters->page * 25;
            $moto_ad = $moto_ad->offset($offset);    
        }

        return $moto_ad
            ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images']);
                    } ,
                    'fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])
            ->limit(25)
            ->get()
            ->toArray();
    }

    public function getMobileHomeAd($filters)
    {
        $mobile_home_ad = new MobileHomeAd;

        $mobile_home_ad = $mobile_home_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->orWhere('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->orWhere('drive_type_id',$filters->drive_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_hp && $filters->to_power_hp){
                $query->orWhereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->from_engine_displacement && $filters->to_engine_displacement){
                $query->orWhereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->orWhere('color',$filters->color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->cylinders) {
                $query->orWhere('cylinders',$filters->seats);
            }
            if ($filters->gears) {
                $query->orWhere('gears',$filters->gears);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });

        if ($filters->oldest) {
            $mobile_home_ad->orderBy('created_at','DESC');
        }
        
        if ($filters->newer) {
            $mobile_home_ad->orderBy('created_at','ASC');
        }

        if ($filters->higher_price) {
            $mobile_home_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $mobile_home_ad->orderBy('price','ASC');
        }

        if (isset($filters->page)) {
            $offset = $filters->page * 25;
            $mobile_home_ad = $mobile_home_ad->offset($offset);    
        }

        return $mobile_home_ad
            ->with(['make','model' => function($query)
                    {
                        $query->with(['generation']);
                    },
                    'ad'=> function($query)
                    {
                        $query->with(['images']);
                    },'generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])
            ->limit(25)
             ->get()
            ->toArray();
    }

    public function getTruckAd($filters)
    {
        $truck_ad = new TruckAd;

        $truck_ad = $truck_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }
            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_kw && $filters->to_power_kw){
                $query->orWhereBetween('power_hp',[$filters->from_power_kw,$filters->to_power_kw]);
            }
            if ($filters->exterior_color) {
                $query->orWhere('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->orWhere('interior_color',$filters->interior_color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });
        
        if ($filters->oldest) {
            $truck_ad->orderBy('created_at','DESC');
        }
        
        if ($filters->newer) {
            $truck_ad->orderBy('created_at','ASC');
        }

        if ($filters->higher_price) {
            $truck_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $truck_ad->orderBy('price','ASC');
        }

        if (isset($filters->page)) {
            $offset = $filters->page * 25;
            $truck_ad = $truck_ad->offset($offset);    
        }

        return $truck_ad
            ->with(['make','fuelType','ad'=> function($query)
                    {
                        $query->with(['images']);
                    },
                'transmissionType','dealer','dealerShowRoom'])
            ->limit(25)
            ->get()
            ->toArray();
    }
    
    public function countSearchAdvanced(Request $request)
    {   
        
        $resource = ApiHelper::resource();
        

        try {
            $counts = 0;
            if ($request->types) {
                foreach ($request->types as $type) {
                    switch ($type) {
                        case 'auto':
                           $counts += $this->getCountAutoAd($request);
                            break;
                        case 'moto':
                            $counts += $this->getCountMotoAd($request);
                            break;
                        case 'mobile-home':
                            $counts += $this->getCountMobileHomeAd($request);
                            break;
                        case 'truck':
                            $counts += $this->getCountTruckAd($request);
                            break;
                        default:
                            break;
                    }
                }
            }
               
            return response()->json(['data' => $counts], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


public function getCountAutoAd($filters)
    {
        $auto_ad = new AutoAd;

        $auto_ad = $auto_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->orWhere('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }
            if ($filters->doors) {
                $query->orWhere('doors',$filters->doors);
            }
            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }
            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('ad_fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('ad_transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->orWhere('ad_drive_type_id',$filters->drive_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_hp && $filters->to_power_hp){
                $query->orWhereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->from_engine_displacement && $filters->to_engine_displacement){
                $query->orWhereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->exterior_color) {
                $query->orWhere('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->orWhere('interior_color',$filters->interior_color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->seats) {
                $query->orWhere('seats',$filters->seats);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });
        
        return $auto_ad->count();
    }

    public function getCountMotoAd($filters)
    {
        $moto_ad = new MotoAd;

        $moto_ad = $moto_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->orWhere('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }
            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->orWhere('drive_type_id',$filters->drive_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_hp && $filters->to_power_hp){
                $query->orWhereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->from_engine_displacement && $filters->to_engine_displacement){
                $query->orWhereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->orWhere('color',$filters->color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->cylinders) {
                $query->orWhere('cylinders',$filters->seats);
            }
            if ($filters->gears) {
                $query->orWhere('gears',$filters->gears);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });
        
        return $moto_ad->count();;
    }

    public function getCountMobileHomeAd($filters)
    {
        $mobile_home_ad = new MobileHomeAd;

        $mobile_home_ad = $mobile_home_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->orWhere('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }
            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->orWhere('drive_type_id',$filters->drive_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_hp && $filters->to_power_hp){
                $query->orWhereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->from_engine_displacement && $filters->to_engine_displacement){
                $query->orWhereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->orWhere('color',$filters->color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->cylinders) {
                $query->orWhere('cylinders',$filters->seats);
            }
            if ($filters->gears) {
                $query->orWhere('gears',$filters->gears);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });

        return $mobile_home_ad->count();
    }

    public function getCountTruckAd($filters)
    {
        $truck_ad = new TruckAd;

        $truck_ad = $truck_ad->where(function($query) use ($filters){
            
            if ($filters->make_id) {
                $query->orWhere('make_id',$filters->make_id);
            }
            if ($filters->country) {
                $query->orWhere('country',$filters->country);
            }
            if ($filters->city) {
                $query->orWhere('city',$filters->city);
            }
            if ($filters->to_mileage && $filters->from_mileage) {
                $query->orWhereBetween('mileage',[$filters->to_mileage,$filters->from_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->orWhereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }
            if ($filters->condition) {
                $query->orWhere('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->orWhere('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->orWhere('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->orWhereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if ($filters->from_power_kw && $filters->to_power_kw){
                $query->orWhereBetween('power_hp',[$filters->from_power_kw,$filters->to_power_kw]);
            }
            if ($filters->exterior_color) {
                $query->orWhere('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->orWhere('interior_color',$filters->interior_color);
            }
            if ($filters->dealer_id) {
                $query->orWhere('dealer_id',$filters->dealer_id);
            }
            if ($filters->inspection_valid_until_month) {
                $query->orWhere('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->orWhere('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if ($filters->owners) {
                $query->orWhere('owners',$filters->owners);
            }
            if ($filters->fuel_consumption) {
                $query->orWhere('fuel_consumption',$filters->fuel_consumption);
            }
        });
        
        return $truck_ad->count();
    }

     public function searchAdvancedMechanic(Request $request)
    {   
        
        $resource = ApiHelper::resource();
        $filter_types = [];
        $response = [];
        
        try {
            
            $mechanic_ads = MechanicAd::join('ads','ads.id','mechanic_ads.ad_id');
            
            //$mechanic_ads = MechanicAd::query();

            $filters = $request->all();

            $mechanic_ads = $mechanic_ads->where(function($query) use ($filters){
            
                if (isset($filters['title'])) {
                    $query->orWhere('ads.title','LIKE', '%'.$filters['title'].'%');
                }
                if (isset($filters['country'])) {
                    $query->orWhere('mechanic_ads.country',$filters['country']);
                }
                if (isset($filters['city'])) {
                    $query->orWhere('mechanic_ads.city','LIKE','%'.$filters['city'].'%');
                }
            });
            
            if (isset($filters['oldest'])) {
                $mechanic_ads->orderBy('created_at','DESC');
            }
            
            if (isset($filters['newer'])) {
                $mechanic_ads->orderBy('created_at','ASC');
            }

            return response()->json(['data' => $mechanic_ads->paginate(25)], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function getPromotedSimpleAdsByUser(Request $request)
    {

        $type = null;
        
        if($request->type == 'vehicle'){
             $type = ['auto','moto','mobile-home','truck'];
        }
        if($request->type == 'service'){
            $type = ['rental','shop','mechanic'];   
        }
        
        $data = Ad::select('ads.*')
            ->join('promoted_simple_ads','ads.id','promoted_simple_ads.ad_id')
            ->where('promoted_simple_ads.user_id',Auth::user()->id);
            
            
        if (!is_null($type)) {
            $data = $data->whereIn('ads.type',$type);
        }

        $data->with([
                    'mechanicAd',
                    'rentalAd',
                    'images',
                    'autoAd' => function($query)
                    {
                        $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'motoAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'mobileHomeAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]);

        return ['data' => $data->get()];
    }

    public function getPromotedFrontPageAdsByUser(Request $request)
    {
        $type = null;
        
        if($request->type == 'vehicle'){
             $type = ['auto','moto','mobile-home','truck'];
        }
        if($request->type == 'service'){
            $type = ['rental','shop','mechanic'];   
        }


        $data = Ad::select('ads.*')
            ->join('promoted_front_page_ads','ads.id','promoted_front_page_ads.ad_id')
            ->where('promoted_front_page_ads.user_id',Auth::user()->id);
        
        if (!is_null($type)) {
            $data = $data->whereIn('ads.type',$type);
        } 

        $data->with([
                    'mechanicAd',
                    'rentalAd',
                    'images',
                    'autoAd' => function($query)
                    {
                        $query->with(['make','model','ad','generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'motoAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                    },
                    'mobileHomeAd' => function($query)
                    {
                        $query->with(['make','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]);

        return ['data' => $data->get()];
    }
}
