<?php

namespace App\Http\Controllers\Admin;

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
        $promoted_simple_ads = RentalAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->inRandomOrder()->limit(25);

        foreach (RentalAd::getRelationships() as $key => $value) {
           $promoted_simple_ads->with($key);
        }

        $promoted = $promoted_simple_ads->get()->toArray();
      
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
                    $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)');

                   $query->with(['ad' => function ($query)
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
                //'thumbnail' => $sanitized['thumbnail'],
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
            
            $rental_ad = RentalAd::create([
                'ad_id' =>  $ad->id,
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
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            Ad::where('id',$ad->id)->update(['thumbnail' => $thumbnail]);

            $images = AdImage::where('ad_id',$ad->id)->get();
            
            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));

            return response()->json(['data' => ['ad' => $ad,'rental_ad' => $rental_ad,'images' => $images]], 200);

        } catch (Exception $e) {
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

           $slug = $this->slugAd($sanitized['title']);

            $ad = Ad::where('id',$id)->update([
                'slug' => $slug,
                'title' => $sanitized['title'],
                'description' => $sanitized['description'],
                //'thumbnail' => $sanitized['thumbnail'],
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
            
            $rental_ad = RentalAd::where('ad_id',$id)->update([
                'ad_id' =>  $ad->id,
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
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            $thumbnail != '' ? Ad::where('id',$id)->update(['thumbnail' => $thumbnail]) : null;

            $images = AdImage::where('ad_id',$ad->id)->get();
            
            $user = Auth::user();

            //$user->notify(new \App\Notifications\NewAd($user));

            return response()->json(['data' => ['ad' => $ad,'rental_ad' => $rental_ad,'images' => $images]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
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

     public function rentalAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','rental')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'rentalAd',
                    ]
                );

        return ['data' => $data->get()];
    }
}
