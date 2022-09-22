<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Redis as Redis;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RentalAd\BulkDestroyRentalAd;
use App\Http\Requests\Admin\RentalAd\DestroyRentalAd;
use App\Http\Requests\Admin\RentalAd\IndexRentalAd;
use App\Http\Requests\Admin\RentalAd\StoreRentalAd;
use App\Http\Requests\Admin\RentalAd\UpdateRentalAd;
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
use App\Models\{Ad,RentalAd,AdImage,DealerShowRoom};

class RentalAdsController extends Controller
{
    use ApiController;

    /**
     * Display a listing of the resource.
     *
     * @param IndexRentalAd $request
     * @return array|Factory|View
     */
    public function index(IndexRentalAd $request)
    {

        if(Redis::exists('rental_ads')) {
            $promoted = json_decode(Redis::get('rental_ads'));

        }else{
            $promoted_simple_ads = RentalAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')
            ->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)')
            ->inRandomOrder()->limit(25);
    
            foreach (RentalAd::getRelationships() as $key => $value) {
               $promoted_simple_ads->with($key);
            }
    
            $promoted = $promoted_simple_ads->get()->toArray();
            Redis::set('rental_ads',json_encode($promoted ));
        }
        
      
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(RentalAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'address','latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address'],

            // set columns to searchIn
            ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }
                    
                    if(isset($request->filters['title'])){
                        $ad_ids = Ad::select('id')
                            ->where('title','LIKE','%'.$request->filters['title'].'%' )
                            ->where('type','rental')
                            ->get()
                            ->toArray();
                        
                        $ids = [];

                        foreach ($ad_ids as $key => $ad_id) {
                            $ids[$key] =  $ad_id['id'];
                        }

                        $query->whereIn('ad_id',$ids);
                    }
                    
                    $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');
                
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

        $data =  RentalAd::whereRaw("ad_id in (SELECT id FROM ads where (ads.title LIKE '%".$filter."%' or ads.description LIKE '%".$filter."%') and type = 'rental')")->with([
                    'ad'=> function($query)
                    {
                        $query->with(['images']);
                    }])->paginate(25);

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
        $this->authorize('admin.rental-ad.create');

        return view('admin.rental-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRentalAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreRentalAd $request)
    {
        try {

            $sanitized = $request->getSanitized();

           $slug = $this->slugAd($sanitized['title']);

            $ad = Ad::create([
                'slug' => $slug,
                'title' => $sanitized['title'],
                'description' => $sanitized['description'],
                'status' => 0,
                'type' => 'rental',
                'is_featured' => 0,
                'user_id' => Auth::user()->id,
                'market_id' => $sanitized['market_id'],
                'external_id' =>null,
                'source' => null,
                'images_processing_status' => 'SUCCESSFUL',
                'images_processing_status_text' => null,
            ]);
            
            $thumbnail = '';
            $i = 0;

            $file = $request->file()[0];
            $thumbnail = $this->uploadFile($file,$ad->id,$i,true);
            $ad->thumbnail = $thumbnail;
            $ad->save();

            $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;
            Redis::del('rental-ads');

            $rental_ad = RentalAd::create([
                'ad_id'  => $ad->id, 
                'address' => $sanitized['address'],
                'latitude' => $sanitized['latitude'] ?? null,
                'longitude' => $sanitized['longitude'] ?? null,
                'zip_code' => $sanitized['zip_code'],
                'dealer_id' => Auth::user()->dealer_id ?? null,
                'dealer_show_room_id' => $dealer_show_room_id,
                'city' => $sanitized['city'],
                'country' =>$sanitized['country'],
                'mobile_number' => $sanitized['mobile_number'],
                'whatsapp_number' => $sanitized['whatsapp_number'],
                'website_url' => $sanitized['website_url'],
                'email_address' => $sanitized['email_address'],
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            Ad::where('id',$ad->id)->update(['thumbnail' => $thumbnail]);

            $images = AdImage::where('ad_id',$ad->id)->get();
            
            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));

            return response()->json(['data' => ['ad' => $ad,'rental_ad' => $rental_ad,'images' => $images]], 200);

        } catch (Exception $e) {
            $ad->delete();
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param RentalAd $rentalAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(RentalAd $rentalAd)
    {
        $this->authorize('admin.rental-ad.show', $rentalAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param RentalAd $rentalAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(RentalAd $rentalAd)
    {
        $this->authorize('admin.rental-ad.edit', $rentalAd);


        return view('admin.rental-ad.edit', [
            'rentalAd' => $rentalAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRentalAd $request
     * @param RentalAd $rentalAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateRentalAd $request,$id)
    {
        try {

            $sanitized = $request->getSanitized();

           
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
                $file = $request->file()[0];
                $thumbnail = $this->uploadFile($file,$ad->id,$i,true);
                $ad->thumbnail = $thumbnail;
                $ad->save();
            }
            
            $rental_ad = RentalAd::where('ad_id',$id)->update([
                'address' => $sanitized['address'],
                'latitude' => $sanitized['latitude'] ?? null,
                'longitude' => $sanitized['longitude'] ?? null,
                'zip_code' => $sanitized['zip_code'],
                'city' => $sanitized['city'],
                'country' =>$sanitized['country'],
                'mobile_number' => $sanitized['mobile_number'],
                'whatsapp_number' => $sanitized['whatsapp_number'],
                'website_url' => $sanitized['website_url'],
                'email_address' => $sanitized['email_address'],
            ]);

            $images = AdImage::where('ad_id',$id)->get();
            
            //$user = Auth::user();

            $rental_ad = RentalAd::where('ad_id',$id)->first();
            //$user->notify(new \App\Notifications\NewAd($user));

            return response()->json(['data' => ['ad' => $ad,'rental_ad' => $rental_ad,'images' => $images]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage().', Line '. $e->getLine());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRentalAd $request
     * @param RentalAd $rentalAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyRentalAd $request, RentalAd $rentalAd)
    {
        $rentalAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyRentalAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyRentalAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    RentalAd::whereIn('id', $bulkChunk)->delete();

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

     public function rentalAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','rental')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'rentalAd' => function($query)
                        {
                            $query->with(['dealer','dealerShowRoom']);
                        },
                    ]
                );

        return ['data' => $data->get()];
    }
}
