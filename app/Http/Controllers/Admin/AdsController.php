<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\NotifyApproved;
use App\Http\Requests\Admin\Ad\BulkDestroyAd;
use App\Http\Requests\Admin\Ad\DestroyAd;
use App\Http\Requests\Admin\Ad\IndexAd;
use App\Http\Requests\Admin\Ad\StoreAd;
use App\Http\Requests\Admin\Ad\UpdateAd;
use App\Models\{Ad,CsvAd,RejectedComment,AdRejectedComment,User};
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

class AdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAd $request
     * @return array|Factory|View
     */
    public function index(IndexAd $request)
    {   
   
        if ($request->all) {
            
            $query = Ad::query();

            $columns =  ['id', 'slug', 'title', 'description', 'thumbnail', 'status','type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','csv_ad_id','created_at'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (Ad::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Ad::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'thumbnail', 'status', 'type',  'status','is_featured', 'user_id', 'market_id', 'external_id', 'source', 'images_processing_status','csv_ad_id'],

            // set columns to searchIn
            ['id', 'slug', 'title', 'description', 'thumbnail', 'status', 'type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','csv_ad_id','created_at'],

        function ($query) use ($request) {
                     
                $columns =  ['id', 'slug', 'title', 'description', 'status', 'thumbnail', 'type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','csv_ad_id','created_at'];

                foreach ($columns as $column) {
                    if ($request->filters) {
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

                if ($request->input('types')) {
                    
                    $query->whereIn('type',$request->input('types'));
                    $where_ad_id = '';
                    $i = 1;

                    foreach ($request->input('types') as $type ) {
                        $table = $this->getTypeAds($type);
                        if ($table) {
                            if ($i == 1) {
                                if ($i ==  count($request->input('types'))) {
                                    $where_ad_id .= sprintf('(ads.id in (SELECT ad_id from %s)) ',$table);
                                }else{
                                    $where_ad_id .= sprintf('(ads.id in (SELECT ad_id from %s) ',$table);
                                }
                            }else{
                                if ($i == count($request->input('types'))) {
                                    $where_ad_id .= sprintf(' or ads.id in (SELECT ad_id from %s)) ',$table);
                                }else{
                                    $where_ad_id .= sprintf(' or ads.id in (SELECT ad_id from %s) ',$table);
                                }
                            }
                            $i++;        
                        }
                    }

                    $query->whereRaw($where_ad_id); 
                }

                foreach (Ad::getRelationships() as $key => $value) {
                    $query->with($key);
                }
            }
        );

        foreach ($data as $key0 => $ad) {
            foreach (Ad::getRelationships() as $key1 => $ad_Relationship) {
                if ($ad[$key1] !== null) {
                    if (get_class($ad[$key1]) != 'Illuminate\Database\Eloquent\Collection') {
                        foreach ($ad[$key1]::getRelationships() as $key2 => $value) {
                            $ad[$key1][$key2] = $ad[$key1][$key2];
                        }
                    }
                }
            }      
        }

        return ['data' => $data];
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
                    ->limit(10)->get();
      
        foreach ($data as $key0 => $ad) {
            foreach (Ad::getRelationships() as $key1 => $ad_Relationship) {
                if ($ad[$key1] !== null) {
                    if (get_class($ad[$key1]) != 'Illuminate\Database\Eloquent\Collection') {
                        foreach ($ad[$key1]::getRelationships() as $key2 => $value) {

                            $ad[$key1][$key2] = $ad[$key1][$key2];
                        }
                    }
                }
            }      
        }

        return ['data' => $data];
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

        return ['data' => $ads->get()];
    }

    public function countAdsToday(Request $request)
    {
        $today = date('Y-m-d');
        $count_ads = Ad::where('created_at','LIKE','%'.$today.'%')->count();
        
        return ['data' => $count_ads];
    }

     public function countAdsImportToday(Request $request)
    {
        $today = date('Y-m-d');
        $sources = [
            'INVENTARIO_IMPORT',
            'MECHANICS_IMPORT',
            'PORTAL',
            'PORTAL_CLUB_IMPORT',
            'RENTALS_IMPORT',
            'WEB_MOBILE_24'
        ];

        $count_ads = Ad::where('created_at','LIKE','%'.$today.'%')->whereIn('source',$sources)->count();
        
        return ['data' => $count_ads];
    }


    public function setApprovedRejected(Request $request,$status)
    {   
        
        $ads = Ad::whereIn('id',$request->ad_ids)
                ->update(
                    [
                        'status' => $status == 'approved' ? 10 : 20
                    ]
                );
        
        $user = User::find($request->user_id);

        //$user->notify(new NotifyApproved);

        return ['data' => $ads];
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
    public function show($ad)
    {   
        $ad = Ad::find($ad);
        
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
        $ad->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
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
            'mobile-home' => 'mobile_home_ads',
            'truck' => 'truck_ads',
            'rental' => 'rental_ads',
            'shop' => 'shop_ads',
            'truck' => 'truck_ads'
        ];
        
        if (isset($type_ads[$key])) {
            return $type_ads[$key];
        }

        return null;
    }
}
