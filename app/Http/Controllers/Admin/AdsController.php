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
use App\Models\{Ad,CsvAd,Dealer,Characteristic,RejectedComment,AdRejectedComment,User,AutoAd,MotoAd,MechanicAd,MobileHomeAd,ShopAd,TruckAd,RentalAd};
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
use Illuminate\Support\Facades\Redis as Redis;

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

       
        /*$promoted = [];


        $promoted_simple_ads = Ad::whereRaw('id in(SELECT ad_id FROM promoted_simple_ads)');
        
        if (isset($request->filters['type'])) {
            $promoted_simple_ads =  $promoted_simple_ads->where('type',$request->filters['type']); 
        }

        $promoted_simple_ads->with([
                    'mechanicAd',
                    'rentalAd',
                    'images',
                    'user',
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

        $promoted = $promoted_simple_ads->inRandomOrder()->limit(25)->get()->toArray();*/
        
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

                $filter_like = $request->filter_like;
                
                if (!is_null($filter_like)) {
                    $query->where(function($query1) use ($filter_like){
                        $query1->where('title','LIKE','%'.$filter_like.'%')
                            ->orWhereRaw("user_id IN(SELECT id FROM users WHERE CONCAT(first_name,' ',last_name) LIKE '%".$filter_like."%')")
                            ->orWhereRaw("user_id IN(SELECT id FROM users WHERE email LIKE '%".$filter_like."%')");
                    });
                }
                
                $query->with(
                    [
                        'user',
                        'characteristics',
                        'mechanicAd'=> function($query)
                        {
                            $query->with(['dealer','dealerShowRoom']);
                        },
                        'rentalAd' => function($query)
                        {
                            $query->with(['dealer','dealerShowRoom']);
                        },
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
                            $query->with(['make','driveType','model','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                        },
                        'truckAd' => function($query)
                        {
                            $query->with(['make','fuelType','driveType','ad','transmissionType','dealer','dealerShowRoom']);
                        },
                        'shopAd' => function($query)
                        {
                            $query->with(['make','model','ad','dealer','dealerShowRoom']);
                        }
                    ]
                );
            }
        );

        //$data = $data->toArray(); 
            
        //array_push($promoted,...$data['data']);
    
        //$data['data'] = $promoted;
      
        return ['data' => $data];
    }


   

    public function searchAdsLike(Request $request)
    {   
        $validator = \Validator::make($request->all(), [
            'filter' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors()],422);
        }

        $filter = $request->filter;
        $type = $request->type;
        
        $ads = Ad::where(function ($query) use ($filter){
            $query->where('ads.title','LIKE','%'. $filter.'%');
        });

        if ($type != 'all') {
            $ads = $ads->where('ads.type',$type);
        }

        $ad_ids = [];

        $ads->with([
            'user',
            'characteristics',
            'mechanicAd' => function($query)
            {
                $query->with(['dealer','dealerShowRoom']);
            },
            'rentalAd' => function($query)
            {
                $query->with(['dealer','dealerShowRoom']);
            },
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
                $query->with(['make','model','driveType','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
            },
            'truckAd' => function($query)
            {
                $query->with(['make','fuelType','driveType','ad','transmissionType','dealer','dealerShowRoom']);
            },
            'shopAd' => function($query)
            {
                $query->with(['make','model','ad','dealer','dealerShowRoom']);
            }
        ]);

        return response()->json([
            'data' => $ads->paginate(24)
        ]);
    }

    public function searchAdsLikeTitle(Request $request)
    {   
        $validator = \Validator::make($request->all(), [
            'filter' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors()],422);
        }

        $filter = $request->filter;
        $type = $request->type;
        
        $ads = Ad::select('id')->where(function ($query) use ($filter){
            $query->where('ads.title','LIKE','%'. $filter.'%')
                  ->orWhere('ads.description','LIKE','%'.$filter.'%');
        })
        ->where('type',$type)
        ->limit(24)
        ->get();

        $ad_ids = [];

        foreach ($ads as $ad) {
            array_push($ad_ids, $ad['id']);
        }
        
        $response = [];
        
        switch ($type) {
            case 'auto':
                $response = AutoAd::whereIn('ad_id',$ad_ids)
                    ->where(function ($query) use ($filter){ 
                        $query->whereRaw("make_id IN(SELECT id FROM makes WHERE name LIKE '%".$filter."%')")
                            ->orWhereRaw("model_id IN(SELECT id FROM models WHERE name LIKE '%".$filter."%')");
                    })
                    ->with(['make',
                            'model',

                            'ad'=> function($query)
                            {
                                $query->with(['images','characteristics']);
                            },
                            'generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                break;
            case 'moto':
                $response = MotoAd::whereIn('ad_id',$ad_ids)
                    ->where(function ($query) use ($filter){ 
                        $query->whereRaw("make_id IN(SELECT id FROM makes WHERE name LIKE '%".$filter."%')")
                            ->orWhereRaw("model_id IN(SELECT id FROM models WHERE name LIKE '%".$filter."%')");
                    })
                    ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    } ,
                    'fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom']);
                break;
            case 'mobile-home':
                $response = MobileHomeAd::whereIn('ad_id',$ad_ids)
                    ->where(function ($query) use ($filter){ 
                        $query->orWhere('custom_make','LIKE','%'.$filter.'%')
                            ->orWhere('custom_model','LIKE','%'.$filter.'%')
                            ->orWhereRaw("make_id IN(SELECT id FROM makes WHERE name LIKE '%".$filter."%')")
                            ->orWhereRaw("model_id IN(SELECT id FROM models WHERE name LIKE '%".$filter."%')");
                    })
                    ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    },
                    'make','model','driveType','fuelType','transmissionType','dealer','dealerShowRoom']);
                break;
            case 'truck':
                $response = TruckAd::whereIn('ad_id',$ad_ids)
                    ->where(function ($query) use ($filter){ 
                        $query->orWhere('custom_make','LIKE','%'.$filter.'%')
                            ->orWhereRaw("make_id IN(SELECT id FROM makes WHERE name LIKE '%".$filter."%')")
                            ->orWhere('model','LIKE','%'.$filter.'%');
                    })
                    ->with(['make','fuelType','ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    },
                'transmissionType','driveType','dealer','dealerShowRoom']);
                break;
            case 'rental':
                $response = RentalAd::whereIn('ad_id',$ad_ids)->with([
                    'ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    },
                    'dealer','dealerShowRoom']);
                break;  
            case 'shop':
                $response = ShopAd::whereIn('ad_id',$ad_ids)
                    ->where(function ($query) use ($filter){ 
                        $query->whereRaw("make_id IN(SELECT id FROM makes WHERE name LIKE '%".$filter."%')")
                            ->orWhereRaw("model_id IN(SELECT id FROM models WHERE name LIKE '%".$filter."%')");
                    })
                    ->with(['make','model','ad'=> function($query)
                        {
                            $query->with(['images','characteristics']);
                        },
                    'dealer','dealerShowRoom']);
                break;
            case 'mechanic':
                $response = MechanicAd::whereIn('ad_id',$ad_ids)->with([
                    'ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    },
                    'dealer','dealerShowRoom']);
                break;     
            default:

                break;
        }
    
        return response()->json([
            'data' => $response->paginate(24)
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
        
        if(Redis::exists('by_user_'.Auth::user()->id.'_filter_'.$request->filter)) {
            $data = json_decode(Redis::get('by_user_'.Auth::user()->id.'_filter_'.$request->filter));
            return ['data' => $data, 'redis'=>'true'];
        }else{
            
            $data = Ad::where('user_id',Auth::user()->id)
            ->orderBy('created_at','DESC')
            ->limit(20);

            if ($request->filter) {
                if ($request->filter == 'auto') {
                    $data = $data->whereIn('type',['auto','mobile-home','moto','truck']);
                }else{
                    $data = $data->where('type',$request->filter);
                }
            } 

            $data->with([
                    'user',
                    'characteristics',
                    'mechanicAd' => function($query)
                    {
                        $query->with(['dealer','dealerShowRoom']);
                    },
                    'rentalAd' => function($query)
                    {
                        $query->with(['dealer','dealerShowRoom']);
                    },
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
                        $query->with(['make','model','driveType','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','fuelType','driveType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]
            );
            $data=$data->get();
            Redis::set('by_user_'.Auth::user()->id.'_filter_'.$request->filter,json_encode($data));
            return ['data' => $data, 'redis'=>'false'];
  
        }

        
    }

    public function byDealer(Request $request,$dealer_id)
    {
        $data = Ad::select('ads.*');

        switch ($request->filter) {
            case 'auto':
                $data = $data->where(function($query) use ($dealer_id){
                    $query->whereRaw("(
                        id in (SELECT ad_id from auto_ads where dealer_id = '".$dealer_id."') or
                        id in (SELECT ad_id from mobile_home_ads where dealer_id = '".$dealer_id."') or 
                        id in (SELECT ad_id from moto_ads where dealer_id = '".$dealer_id."') or
                        id in (SELECT ad_id from truck_ads where dealer_id = '".$dealer_id."')
                    )")->where('ads.status','10');
                });
                break;
            case 'mechanic':
                $data = $data->join('mechanic_ads','mechanic_ads.ad_id','ads.id')
                    ->where('dealer_id',$dealer_id)
                    ->where('status','10');
                break;
            case 'rental':
                $data = $data->join('rental_ads','rental_ads.ad_id','ads.id')
                    ->where('dealer_id',$dealer_id)
                    ->where('status','10');
                break;
            case 'shop':
                $data = $data ->join('shop_ads','shop_ads.ad_id','ads.id')
                    ->where('dealer_id',$dealer_id)
                    ->where('status','10');
                break;
            default:
                
                break;
        }

        $data->with([
            'user',
            'characteristics',
            'mechanicAd' => function($query)
            {
                $query->with(['dealer','dealerShowRoom']);
            },
            'rentalAd' => function($query)
            {
                $query->with(['dealer','dealerShowRoom']);
            },
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
                $query->with(['make','model','ad','driveType','fuelType','transmissionType','dealer','dealerShowRoom']);
            },
            'truckAd' => function($query)
            {
                $query->with(['make','fuelType','driveType','ad','transmissionType','dealer','dealerShowRoom']);
            },
            'shopAd' => function($query)
            {
                $query->with(['make','model','ad','dealer','dealerShowRoom']);
            }
        ]);
      
        return ['data' => $data->paginate(10)];
    }

    public function byDealerCount(Request $request,$dealer_id)
    {
        try {

            $product_ads = Ad::where(function($query) use ($dealer_id){
                $query->whereRaw("(
                    id in (SELECT ad_id from auto_ads where dealer_id = '".$dealer_id."') or
                    id in (SELECT ad_id from mobile_home_ads where dealer_id = '".$dealer_id."') or 
                    id in (SELECT ad_id from moto_ads where dealer_id = '".$dealer_id."' ) or
                    id in (SELECT ad_id from truck_ads where dealer_id = '".$dealer_id."')
                )")->where('ads.status','10');
            })->count();

            $service_ads = Ad::where(function($query) use ($dealer_id){
                $query->whereRaw("(
                    id in (SELECT ad_id from mechanic_ads where dealer_id = '".$dealer_id."') or
                    id in (SELECT ad_id from rental_ads where dealer_id = '".$dealer_id."') or 
                    id in (SELECT ad_id from shop_ads where dealer_id = '".$dealer_id."')
                )")->where('ads.status','10');
            })->count();

            return response()->json(['data' => ['product_ads' => $product_ads, 'service_ads' => $service_ads]], 200);
        
        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
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

        if (!is_null($request->filter_like)) {
            
            $filter_like = $request->filter_like;

            $ads->where(function($query1) use ($filter_like){
                $query1->orWhereRaw("user_id IN(SELECT id FROM users WHERE CONCAT(first_name,' ',last_name) LIKE '%".$filter_like."%')")
                    ->orWhereRaw("user_id IN(SELECT id FROM users WHERE email LIKE '%".$filter_like."%')");
            });
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
                if ($status == 'approved') {
                    $user->notify(new NotifyApproved($ad->title));
                }
            }
            Redis::flushDB();
               
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
            
            if ($status == 'approved') {
                $user->notify(new NotifyApproved($ad_->title));
            }
        }
        Redis::flushDB();

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
 
        $resource = ApiHelper::resource();
        
        try {

            $ad = Ad::where('id',$id);
        
            if (is_null($ad)) {
                ApiHelper::setError($resource, 0, 404 ,['data' => 'Ad not found']);
                return $this->sendResponse($resource);
            }

           

            $ad->with([
                'user',
                'characteristics',
                'mechanicAd' => function($query)
                {
                    $query->with(['dealer','dealerShowRoom']);
                },
                'rentalAd' => function($query)
                {
                    $query->with(['dealer','dealerShowRoom']);
                },
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
                    $query->with(['make','model','ad','driveType','vehicleCategory','fuelType','transmissionType','dealer','dealerShowRoom']);
                },
                'truckAd' => function($query)
                {
                    $query->with(['make','vehicleCategory','driveType','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                },
                'shopAd' => function($query)
                {
                    $query->with(['make','model','ad','dealer','dealerShowRoom']);
                }
            ]);
            
            $ad =  $ad->first();

            $ad['characteristic_ads'] = $this->characteristic_ads($ad);
     
            return response()->json(['data' => $ad], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function characteristic_ads($ad){

        $data_sub_characteristic_ids = $ad->characteristics()->select('characteristic_id')->get()->toArray();
        
        $sub_characteristic_ids = [];

        foreach ($data_sub_characteristic_ids as $sub_characteristic_id) {
            array_push($sub_characteristic_ids, $sub_characteristic_id['characteristic_id']);
        }

        $characteristics = Characteristic::whereIn('id',$sub_characteristic_ids)
            ->with([
                'sub_characteristics' => function ($query) use ($ad)
                {
                    $query->whereRaw("sub_characteristics.id in(SELECT sub_characteristic_id FROM ad_sub_characteristic WHERE ad_id='".$ad->id."')");
                }
            ]);

        return $characteristics->get();
    }    


    public function storeCommentRejected(Request $request,$ad)
    {   
        $ad = Ad::find($ad);
        
        $rejected_comment = new RejectedComment;
        $rejected_comment->comment = $request->comment;
        $rejected_comment->save();

        $user = User::find($ad->user_id);

        $user->notify(new NotifyRejected($ad->title,$request->comment));

        $ad_rejected_comment = new AdRejectedComment;
        $ad_rejected_comment->ad_id = $ad->id;
        $ad_rejected_comment->rejected_comment_id = $rejected_comment->id;
        $ad_rejected_comment->save();
        Redis::flushDB();
        
        return ['data' => 'OK'];
    }

     public function storeCommentsRejected(Request $request,$csv_ad_id)
    {   
        $rejected_comment = new RejectedComment;
        $rejected_comment->comment = $request->comment;
        $rejected_comment->save();

        $ads = Ad::where('csv_ad_id',$csv_ad_id)->get();

        foreach ($ads as $ad) {
            $user = User::find($ad->user_id);

            $user->notify(new NotifyRejected($ad->title,$request->comment));
            
            $ad_rejected_comment = new AdRejectedComment;
            $ad_rejected_comment->ad_id = $ad->id;
            $ad_rejected_comment->rejected_comment_id = $rejected_comment->id;
            $ad_rejected_comment->save();
        }
        Redis::flushDB();
        
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

            $ad_ = Ad::find($ad);
            
            $user = User::find($ad_->user_id);

            $user->notify(new NotifyRejected($ad_->title,$request->comment));

            $ad_rejected_comment = new AdRejectedComment;
            $ad_rejected_comment->ad_id = $ad;
            $ad_rejected_comment->rejected_comment_id = $rejected_comment->id;
            $ad_rejected_comment->save();
        }
        Redis::flushDB();

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
    public function destroy(DestroyAd $request,Ad $ad)
    {   
        $resource = ApiHelper::resource();
        try {
            Redis::del('search_advanced_auto');
            Redis::del('search_advanced_moto');
            Redis::del('search_advanced_mobile-home');
            Redis::del('search_advanced_truck');

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            switch ($ad->type) {
                case 'auto':
                    Redis::del('auto_ads');
                    Redis::del('auto_ads_ult');
                    Redis::del('by_user_'.$ad->user_id.'_filter_auto');
                    AutoAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'moto':
                    Redis::del('by_user_'.$ad->user_id.'_filter_auto');
                    MotoAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'mobile-home':
                    Redis::del('by_user_'.$ad->user_id.'_filter_auto');
                    MobileHomeAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'truck':
                    Redis::del('by_user_'.$ad->user_id.'_filter_auto');
                    TruckAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'rental':
                    Redis::del('rental_ads');
                    Redis::del('by_user_'.$ad->user_id.'_filter_rental');
                    RentalAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'shop':
                    Redis::del('shop_ads');
                    Redis::del('by_user_'.$ad->user_id.'_filter_shop');
                    ShopAd::where('ad_id',$ad->id)->delete();
                    break;
                case 'mechanic':
                    Redis::del('mechanic_ads');
                    Redis::del('by_user_'.$ad->user_id.'_filter_mechanic');
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
                    Redis::del('by_user_'.$ad->user_id.'_filter_mechanic');
                    Redis::del('by_user_'.$ad->user_id.'_filter_shop');
                    Redis::del('by_user_'.$ad->user_id.'_filter_rental');
                    Redis::del('by_user_'.$ad->user_id.'_filter_auto');
                    Redis::del('mechanic_ads');
                    Redis::del('shop_ads');
                    Redis::del('rental_ads');
                    Redis::del('auto_ads');
                    Redis::del('auto_ads_ult');
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

    public function getPromotedAds($type,$make_id)
    {   
        $data = null;
       
        switch ($type) {
            case 'auto':
                
                $data = AutoAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')
                        ->where('make_id',$make_id)
                        ->with(['make',
                            'model',
                            'ad'=> function($query)
                            {
                                $query->with(['images','characteristics']);
                            },
                            'generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])
                    ->inRandomOrder()
                    ->limit(10)
                    ->get()
                    ->toArray();

                break;
            case 'moto':

                $data = MotoAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')
                ->where('make_id',$make_id)->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    } ,
                    'fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])
                    ->inRandomOrder()
                    ->limit(10)
                    ->get()
                    ->toArray();

                break;
            case 'mobile-home':

                $data = MobileHomeAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')
                    ->where('make_id',$make_id)
                    ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    },
                    'make','model','ad','fuelType','driveType','transmissionType','dealer','dealerShowRoom'])
                    ->inRandomOrder()
                    ->limit(10)
                    ->get()
                    ->toArray();

                break;
            case 'truck':
                
                $data = TruckAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')
                ->where('make_id',$make_id)
                ->with(['make','fuelType','ad'=> function($query)
                    {
                        $query->with(['images','characteristics']);
                    },
                'transmissionType','driveType','dealer','dealerShowRoom'])
                ->inRandomOrder()
                ->limit(10)
                ->get()
                ->toArray();
                
                break;
            default:
                # code...
                break;
        }

        if (!is_null($data)) {
            return $data;
        }

        return null;
    }

    public function searchAdvanced(Request $request)
    {   
        $resource = ApiHelper::resource();
        $filter_types = [];
        $response = [];
        $val_redis=false;
        try {
            $dealer_id = null;
            
            $promotedAds = $this->getPromotedAds($request->type,$request->make_id);
            
            if (!is_null($promotedAds)) {
                array_push($response, ...$promotedAds);
            }

            foreach ($request->request as $key => $data) {
                if($request->query->get('page') != 1){
                    break;
                }
                if( $key == 'type'){
                    continue;
                }
                
                if(
                    ($request->from_mileage == 0 && $request->to_mileage == 500000) &&
                    ($request->from_price == 500 && $request->to_price == 5000000)
                ){
                    $request['from_mileage'] = null;
                    $request['to_price'] = null;
                    $request['to_mileage'] = null;
                }
                
                if(
                    !($request->from_mileage == 0 && $request->to_mileage == 500000) ||
                    !($request->from_price == 500 && $request->to_price == 5000000)
                ){
                    break;
                }
                if($key == 'from_mileage' || $key == 'to_mileage' || $key == 'from_price' || $key == 'to_price'){
                    continue;
                }
                
                if(!is_null($data) && $data != false && $key != 'newer'){
                    
                    break;
                }
                if($key == 'newer'){
                    if($data == true){
                        if(Redis::exists('search_advanced_'.$request->type)){
                            $data = json_decode(Redis::get('search_advanced_'.$request->type));
                            return response()->json(['data' => $data,'redis'=>'true'], 200);
                        }
                        $val_redis=true;
                    }else{
                        break;
                    }
                }
            }

            
            if ($request->dealer) {
                $dealers = Dealer::select('id')->where('company_name','LIKE','%'.$request->dealer.'%')->get()->toArray();
                foreach ($dealers as $key => $dealer) {
                    $dealer_id[$key] = $dealer['id'];
                }
            }
            $request['dealer_id'] = $dealer_id;

            if ($request->type) {
                
                switch ($request->type) {
                    case 'auto':
                        $response = $this->getAutoAd($request);
                        break;
                    case 'moto':
                        $response = $this->getMotoAd($request);
                        break;
                    case 'mobile-home':
                        $response = $this->getMobileHomeAd($request);
                        break; 
                    case 'truck':
                        $response = $this->getTruckAd($request);
                        break;
                    default:
                        
                        break;
                }
                
            }

            if($val_redis){
                Redis::set('search_advanced_'.$request->type,json_encode($response ));
            }
               
            return response()->json(['data' => $response,'redis'=>false], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage().' '.$e->getLine());
            return $this->sendResponse($resource);
        }
    }


   
    public function getAutoAd($filters)
    {
        $auto_ad = new AutoAd;
         
        $auto_ad = $auto_ad->where(function($query) use ($filters){
            
            if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }

            if ($filters->model_id) {
                $query->where('model_id',$filters->model_id);
            }

            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }

            if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }

            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
             if (!is_null($filters->doors)) {
                $query->whereBetween('doors',[$filters->doors,$filters->doors+1]);
            }
            if ($filters->fuel_type_id) {
                $query->where('ad_fuel_type_id',$filters->fuel_type_id);
            }

            if ($filters->fuel_type_id) {
                $query->where('ad_fuel_type_id',$filters->fuel_type_id);
            }

            if ($filters->body_type_id) {
                $query->where('ad_body_type_id',$filters->body_type_id);
            }

            if ($filters->transmission_type_id) {
                $query->where('ad_transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->where('ad_drive_type_id',$filters->drive_type_id);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if (!is_null($filters->from_engine_displacement) && !is_null($filters->to_engine_displacement)){
                $query->whereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->exterior_color) {
                $query->where('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->where('interior_color',$filters->interior_color);
            }
            if ($filters->inspection_valid_until_month) {
                $query->where('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
            if (!is_null($filters->seats)) {
                $query->where('seats',$filters->seats);
            }
            if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
            if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
        });

        $auto_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');

        if ($filters->oldest) {
            $auto_ad->orderBy('created_at','ASC');
        }
        
        if ($filters->newer) {
            $auto_ad->orderBy('created_at','DESC');
        }

        if ($filters->higher_price) {
            $auto_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $auto_ad->orderBy('price','ASC');
        }

        return $auto_ad
            ->with(['make',
                    'model',
                    'ad'=> function($query)
                    {
                        $query->with(['images','user']);
                    },'generation','series','equipment','fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])
            ->paginate(24)
            ->toArray();
    }

    public function getMotoAd($filters)
    {
        $moto_ad = new MotoAd;

        $moto_ad = $moto_ad->where(function($query) use ($filters){
            if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            if ($filters->model_id) {
                $query->where('model_id',$filters->model_id);
            }
            
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }
            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }
            if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }
            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->where('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->body_type_id) {
                $query->where('body_type_id',$filters->body_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->where('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->where('drive_type_id',$filters->drive_type_id);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if (!is_null($filters->from_engine_displacement) && !is_null($filters->to_engine_displacement)){
                $query->whereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->where('color',$filters->color);
            }
            
            if ($filters->inspection_valid_until_month) {
                $query->where('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
             if (!is_null($filters->cylinders)) {
                $query->where('cylinders',$filters->cylinders);
            }
             if (!is_null($filters->gears)) {
                $query->where('gears',$filters->gears);
            }
             if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
             if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
        });
        
        $moto_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');

        if ($filters->newer) {
            $moto_ad->orderBy('created_at','DESC');
        }
        
        if ($filters->oldest) {
            $moto_ad->orderBy('created_at','ASC');
        }

        if ($filters->higher_price) {
            $moto_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $moto_ad->orderBy('price','ASC');
        }


        return $moto_ad
            ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images','user','characteristics']);
                    } ,
                    'fuelType','bodyType','transmissionType','driveType','dealer','dealerShowRoom'])
            ->paginate(24)
            ->toArray();
    }

    public function getMobileHomeAd($filters)
    {
        $mobile_home_ad = new MobileHomeAd;

        $mobile_home_ad = $mobile_home_ad->where(function($query) use ($filters){
            if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            
            if ($filters->vehicle_category_id) {
                $query->where('vehicle_category_id',$filters->vehicle_category_id);
            }
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->where('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }
             if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }
            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }

            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->where('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->where('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->where('drive_type_id',$filters->drive_type_id);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if (!is_null($filters->from_engine_displacement) && !is_null($filters->to_engine_displacement)){
                $query->whereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->where('color',$filters->color);
            }
            
            if (!is_null($filters->sleeping_places)) {
                $query->where('sleeping_places',$filters->sleeping_places);
            }
            
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
             if (!is_null($filters->cylinders)) {
                $query->where('cylinders',$filters->cylinders);
            }
             if (!is_null($filters->gears)) {
                $query->where('gears',$filters->gears);
            }
             if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
            if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
        });

        $mobile_home_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');

        if ($filters->newer) {
            $mobile_home_ad->orderBy('created_at','DESC');
        }
        
        if ($filters->oldest) {
            $mobile_home_ad->orderBy('created_at','ASC');
        }

        if ($filters->higher_price) {
            $mobile_home_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $mobile_home_ad->orderBy('price','ASC');
        }

        


        return $mobile_home_ad
            ->with(['make','model',
                    'ad'=> function($query)
                    {
                        $query->with(['images','user','characteristics']);
                    },
                    'make','model','driveType','fuelType','transmissionType','dealer','dealerShowRoom'])

            ->paginate(24)
            ->toArray();
    }

    public function getTruckAd($filters)
    {
        $truck_ad = new TruckAd;

        $truck_ad = $truck_ad->where(function($query) use ($filters){
            if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }
            if ($filters->vehicle_category_id) {
                $query->where('vehicle_category_id',$filters->vehicle_category_id);
            }
            if ($filters->category) {
                $query->whereRaw("vehicle_category_id IN (SELECT id FROM vehicle_categories WHERE category = '".$filters->category."')" );
            }
            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }
             if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }
            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->where('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->where('transmission_type_id',$filters->transmission_type_id);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_kw',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->exterior_color) {
                $query->where('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->where('interior_color',$filters->interior_color);
            }
            
            if ($filters->inspection_valid_until_month) {
                $query->where('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
             if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
             if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
        });
        
        $truck_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');

        if ($filters->newer) {
            $truck_ad->orderBy('created_at','DESC');
        }
        
        if ($filters->oldest) {
            $truck_ad->orderBy('created_at','ASC');
        }

        if ($filters->higher_price) {
            $truck_ad->orderBy('price','DESC');
        }
        
        if ($filters->lower_price) {
            $truck_ad->orderBy('price','ASC');
        }

        return $truck_ad
            ->with(['make','fuelType','ad'=> function($query)
                    {
                        $query->with(['images','user','characteristics']);
                    },
                'transmissionType','driveType','dealer','dealerShowRoom'])
            ->paginate(24)
            ->toArray();
    }
    
    public function countSearchAdvanced(Request $request)
    {   
        
        $resource = ApiHelper::resource();
        

        try {

            $dealer_id = null;
            
            if ($request->dealer) {
                
                $dealers = Dealer::select('id')
                	->where('company_name','LIKE','%'.$request->dealer.'%')
                	->get()
                	->toArray();
                
                foreach ($dealers as $key => $dealer) {
                    $dealer_id[$key] = $dealer['id'];
                }
            }
            
            $request['dealer_id'] = $dealer_id;
            
            $counts = 0;
            
            $type = $request->type;

            switch ($type) {
                case 'auto':
                    $counts = ($this->getCountAutoAd($request) * 2) + MotoAd::count() + MobileHomeAd::count() + TruckAd::count() + ShopAd::count() + RentalAd::count() + MechanicAd::count();
                    break;
                case 'moto':
                    $counts = $this->getCountMotoAd($request);
                    break;
                case 'mobile-home':
                    $counts = $this->getCountMobileHomeAd($request);
                    break;
                case 'truck':
                    $counts = $this->getCountTruckAd($request);
                    break;
                case 'all':
                    $counts = (AutoAd::whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)')->count() * 2) + MotoAd::count() + MobileHomeAd::count() + TruckAd::count() + ShopAd::count() + RentalAd::count() + MechanicAd::count();
                    break;
                default:
                    break;
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
            if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->where('model_id',$filters->model_id);
            }

            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }
             if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }
            if (!is_null($filters->doors)) {
                $query->whereBetween('doors',$filters->doors,$filters->doors+1);
            }
            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }
            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->where('ad_fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->body_type_id) {
                $query->where('body_type_id',$filters->body_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->where('ad_transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->where('ad_drive_type_id',$filters->drive_type_id);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if (!is_null($filters->from_engine_displacement) && !is_null($filters->to_engine_displacement)){
                $query->whereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->exterior_color) {
                $query->where('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->where('interior_color',$filters->interior_color);
            }
            
            if ($filters->inspection_valid_until_month) {
                $query->where('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
             if (!is_null($filters->seats)) {
                $query->where('seats',$filters->seats);
            }
             if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
             if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
        });
        
        $auto_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');
        
        return $auto_ad->count();
    }

    public function getCountMotoAd($filters)
    {
        $moto_ad = new MotoAd;

        $moto_ad = $moto_ad->where(function($query) use ($filters){
             if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->where('model_id',$filters->model_id);
            }
            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->body_type_id) {
                $query->where('body_type_id',$filters->body_type_id);
            }
            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }
            if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }
            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->where('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->where('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->where('drive_type_id',$filters->drive_type_id);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if (!is_null($filters->from_engine_displacement) && !is_null($filters->to_engine_displacement)){
                $query->whereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->where('color',$filters->color);
            }
            
            if ($filters->inspection_valid_until_month) {
                $query->where('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
             if (!is_null($filters->cylinders)) {
                $query->where('cylinders',$filters->cylinders);
            }
             if (!is_null($filters->gears)) {
                $query->where('gears',$filters->gears);
            }
             if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
             if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
        });
        
        $moto_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');

        return $moto_ad->count();
    }

    public function getCountMobileHomeAd($filters)
    {
        $mobile_home_ad = new MobileHomeAd;

        $mobile_home_ad = $mobile_home_ad->where(function($query) use ($filters){
             if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }
            if ($filters->model_id) {
                $query->where('model_id',$filters->model_id);
            }
            if ($filters->vehicle_category_id) {
                $query->where('vehicle_category_id',$filters->vehicle_category_id);
            }
            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }
            if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }
            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->where('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->where('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->drive_type_id) {
                $query->where('drive_type_id',$filters->drive_type_id);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_hp',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if (!is_null($filters->from_engine_displacement) && !is_null($filters->to_engine_displacement)){
                $query->whereBetween('engine_displacement',[$filters->from_engine_displacement,$filters->to_engine_displacement]);
            }
            if ($filters->color) {
                $query->where('color',$filters->color);
            }
            if (!is_null($filters->sleeping_places)) {
                $query->where('sleeping_places',$filters->sleeping_places);
            }
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
            
            if ($filters->inspection_valid_until_month) {
                $query->where('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
             if (!is_null($filters->cylinders)) {
                $query->where('cylinders',$filters->cylinders);
            }
             if (!is_null($filters->gears)) {
                $query->where('gears',$filters->gears);
            }
             if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
             if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
        });

        $mobile_home_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');

        return $mobile_home_ad->count();
    }

    public function getCountTruckAd($filters)
    {
        $truck_ad = new TruckAd;

        $truck_ad = $truck_ad->where(function($query) use ($filters){
             if($filters->dealer) {
                $query->whereRaw("dealer_id IN(SELECT id FROM dealers WHERE company_name LIKE '%".$filters->dealer."%')");
            }
            
            if ($filters->make_id) {
                $query->where('make_id',$filters->make_id);
            }
            if ($filters->country) {
                $query->where('country',$filters->country);
            }
            if ($filters->vehicle_category_id) {
                $query->where('vehicle_category_id',$filters->vehicle_category_id);
            }
            
            if ($filters->category) {
                $query->whereRaw("vehicle_category_id IN (SELECT id FROM vehicle_categories WHERE category = '".$filters->category."')" );
            }

            if ($filters->city) {
                $query->where('city','LIKE','%'.$filters->city.'%');
                $query->where('address','LIKE','%'.$filters->city.'%');
            }
             if (!is_null($filters->from_mileage) && !is_null($filters->to_mileage)) {
                $query->whereBetween('mileage',[$filters->from_mileage ,$filters->to_mileage]);
            }

            if ($filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->whereBetween('first_registration_year',[$filters->from_first_registration_year,$filters->to_first_registration_year]);
            }

            if (!$filters->to_first_registration_year && $filters->from_first_registration_year) {
                $query->where('first_registration_year','>=',$filters->from_first_registration_year);
            }
            
            if ($filters->to_first_registration_year && !$filters->from_first_registration_year) {
                $query->where('first_registration_year','<=',$filters->to_first_registration_year);
            }
            if ($filters->condition) {
                $query->where('condition',$filters->condition);
            }
            if ($filters->fuel_type_id) {
                $query->where('fuel_type_id',$filters->fuel_type_id);
            }
            if ($filters->transmission_type_id) {
                $query->where('transmission_type_id',$filters->transmission_type_id);
            }
            if ($filters->from_price && $filters->to_price) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_price) && !is_null($filters->to_price)) {
                $query->whereBetween('price',[$filters->from_price,$filters->to_price]);
            }
            if (!is_null($filters->from_power_hp)  && !is_null($filters->to_power_hp)){
                $query->whereBetween('power_kw',[$filters->from_power_hp,$filters->to_power_hp]);
            }
            if ($filters->exterior_color) {
                $query->where('exterior_color',$filters->exterior_color);
            }
            if ($filters->interior_color) {
                $query->where('interior_color',$filters->interior_color);
            }
            
            if ($filters->inspection_valid_until_month) {
                $query->where('inspection_valid_until_month',$filters->inspection_valid_until_month);
            }
            if ($filters->inspection_valid_until_year) {
                $query->where('inspection_valid_until_year',$filters->inspection_valid_until_year);
            }
             if (!is_null($filters->owners)) {
                $query->where('owners',$filters->owners);
            }
            
            if (!is_null($filters->from_co2_emissions) && !is_null($filters->to_co2_emissions)){
                $query->whereBetween('co2_emissions',[$filters->from_co2_emissions,$filters->to_co2_emissions]);
            }
            /*if (!is_null($filters->co2_emissions)) {
                $query->where('co2_emissions',$filters->co2_emissions);
            }*/
            if (!is_null($filters->fuel_consumption)) {
                $query->where('fuel_consumption',$filters->fuel_consumption);
            }
        });
        
        $truck_ad->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');
        
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
                    $query->where('ads.title','LIKE', '%'.$filters['title'].'%');
                }
                if (isset($filters['country'])) {
                    $query->where('mechanic_ads.country',$filters['country']);
                }
                if (isset($filters['city'])) {
                    $query->where('mechanic_ads.city','LIKE','%'.$filters['city'].'%');
                }
            })->with(['dealer','dealerShowRoom']);
            
            if (isset($filters['oldest'])) {
                $mechanic_ads->orderBy('created_at','DESC');
            }
            
            if (isset($filters['newer'])) {
                $mechanic_ads->orderBy('created_at','ASC');
            }

            return response()->json(['data' => $mechanic_ads->paginate(24)], 200);

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
                    'user',
                    'characteristics',
                    'mechanicAd' => function($query)
                    {
                        $query->with(['dealer','dealerShowRoom']);
                    },
                    'rentalAd' => function($query)
                    {
                        $query->with(['dealer','dealerShowRoom']);
                    },
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
                        $query->with(['make','model','driveType','ad','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','driveType','fuelType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]);

        return ['data' => $data->paginate(24)];
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
                    'user',
                    'characteristics',
                    'mechanicAd' => function($query)
                    {
                        $query->with(['dealer','dealerShowRoom']);
                    },
                    'rentalAd' => function($query)
                    {
                        $query->with(['dealer','dealerShowRoom']);
                    },
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
                        $query->with(['make','model','ad','driveType','fuelType','transmissionType','dealer','dealerShowRoom']);
                    },
                    'truckAd' => function($query)
                    {
                        $query->with(['make','fuelType','driveType','ad','transmissionType','dealer','dealerShowRoom']);
                    },
                    'shopAd' => function($query)
                    {
                        $query->with(['make','model','ad','dealer','dealerShowRoom']);
                    }
                ]);

        return ['data' => $data->paginate(24)];
    }
}
