<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShopAd\BulkDestroyShopAd;
use App\Http\Requests\Admin\ShopAd\DestroyShopAd;
use App\Http\Requests\Admin\ShopAd\IndexShopAd;
use App\Http\Requests\Admin\ShopAd\StoreShopAd;
use App\Http\Requests\Admin\ShopAd\UpdateShopAd;
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
use App\Models\{Ad,ShopAd,DealerShowRoom,AdImage};

class ShopAdsController extends Controller
{
    use ApiController;

    /**
     * Display a listing of the resource.
     *
     * @param IndexShopAd $request
     * @return array|Factory|View
     */
    public function index(IndexShopAd $request)
    {
        if ($request->all) {
            
            $query = ShopAd::query();

            $columns =  ['id', 'ad_id', 'category', 'make_id', 'model', 'manufacturer', 'code', 'condition', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
           
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (ShopAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ShopAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'category', 'make_id', 'model', 'manufacturer', 'code', 'condition', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            // set columns to searchIn
            ['id', 'ad_id', 'category', 'make_id', 'model', 'manufacturer', 'code', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'category', 'make_id', 'model', 'manufacturer', 'code', 'condition', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
                
                    if ($request->filters) {
                        foreach ($columns as $column) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (ShopAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.shop-ad.create');

        return view('admin.shop-ad.create');
    }


    public function byUser(Request $request)
    {

        $data = Ad::where('user_id',Auth::user()->id)
                    ->orderBy('created_at','DESC')
                    ->limit(20);
      
        $data->with(
            ['ad','make','dealer','dealerShowRoom']
        );
      
        return ['data' => $data->get()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreShopAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreShopAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        $ad = Ad::create([
            'slug' => Str::slug($sanitized['title']),
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

        $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id']  : null;
        // Store the ShopAd
        $shopAd = ShopAd::create([
            'category' => $sanitized['category'],
            'make_id' => $sanitized['make_id'],
            'model' => $sanitized['model'],
            'manufacturer' => $sanitized['manufacturer'],
            'code' => $sanitized['code'],
            'condition' => $sanitized['condition'],
            'price' => $sanitized['price'],
            'price_contains_vat' => $sanitized['price_contains_vat'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' => $sanitized['first_name'],
            'last_name' => $sanitized['last_name'],
            'email_address' => $sanitized['email_address'],
            'address' => $sanitized['address'],
            'zip_code' =>$sanitized['zip_code'],
            'city' => $sanitized['city'],
            'country' => $sanitized['country'],
            'latitude' => $sanitized['latitude'],
            'longitude' => $sanitized['longitude'],
            'mobile_number' => $sanitized['mobile_number'],
            'landline_number' => $sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' => $sanitized['youtube_link']
        ]);

        return ['data' =>['ad' =>$ad,'shop_ad' => $shopAd]];
    }

    public function principal_data(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'category' => ['required', 'string'],
            'make_id' => ['required', 'string'],
            'model_id' => ['nullable', 'string'],
            'manufacturer' => ['required', 'string'],
            'code' => ['nullable', 'string'],
            'condition' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
            
            //INSERT INTO `ads` (`id`, `slug`, `title`, `description`, `thumbnail`, `status`, `type`, `is_featured`, `user_id`, `market_id`, `created_at`, `updated_at`, `external_id`, `source`, `images_processing_status`, `images_processing_status_text`, `csv_ad_id`) VALUES ('.', '.', '.', '.', '.', '0', '', '0', '23bcf97c-296b-46c9-bdd5-8057e052bfce', '5b8fa498-efe4-4c19-90a8-7285901b4585', '2022-07-02 09:28:15', '2021-05-15 09:28:15', '26409', NULL, 'N/A', NULL, NULL);

            $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;

            $shopAd = ShopAd::create([
                'ad_id' =>  '.',
                'email_address' =>  '.',
                'address' =>  '.',
                'zip_code' =>  '.',
                'city' =>  '.',
                'country' =>  '.',
                'price' =>  0,
                'category' => $request['category'],
                'make_id' => $request['make_id'],
                'model_id' => $request['model_id'],
                'manufacturer' => $request['manufacturer'],
                'code' => $request['code'],
                'condition' => $request['condition'],
               
            ]);

            return response()->json(['data' => $shopAd], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


    public function details_ads(Request $request)
    {
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'shop_ad_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            //'thumbnail' => ['nullable', 'string'],
            'market_id' => ['required', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            //'files' => ['required','array']
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
                'type' => 'auto',
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

            ShopAd::where('id',$request['shop_ad_id'])->update([
                'ad_id' =>  $ad->id,
                'youtube_link' =>  $request->youtube_link,
                'price' =>  $request->price,
            ]);

            Ad::where('id',$ad->id)->update(['thumbnail' => $thumbnail]);
            
            $shopAd = ShopAd::find($request['shop_ad_id']);

            $images = AdImage::where('ad_id',$ad->id)->get();

            return response()->json(['data' => ['ad' => $ad,'shop_ad' => $shopAd,'images' => $images]], 200);

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
            'shop_ad_id' => ['required', 'string'],
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
            
            ShopAd::where('id',$request['shop_ad_id'])->update([
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

            $shopAd = ShopAd::find($request['shop_ad_id']);
            
            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));

            return response()->json(['data' => $shopAd], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param ShopAd $shopAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(ShopAd $shopAd)
    {
        $this->authorize('admin.shop-ad.show', $shopAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ShopAd $shopAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ShopAd $shopAd)
    {
        $this->authorize('admin.shop-ad.edit', $shopAd);


        return view('admin.shop-ad.edit', [
            'shopAd' => $shopAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateShopAd $request
     * @param ShopAd $shopAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateShopAd $request, ShopAd $shopAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ShopAd
        $shopAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/shop-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/shop-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyShopAd $request
     * @param ShopAd $shopAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyShopAd $request, ShopAd $shopAd)
    {
        $shopAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyShopAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyShopAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ShopAd::whereIn('id', $bulkChunk)->delete();

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
