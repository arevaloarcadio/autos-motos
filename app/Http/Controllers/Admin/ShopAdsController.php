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
use App\Models\{Ad,ShopAd,DealerShowRoom,AdImage,Models};
use Illuminate\Support\Facades\Redis as Redis;

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
    /*    $promoted_simple_ads = ShopAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')
            ->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)')
            ->inRandomOrder()
            ->limit(25);

        foreach (ShopAd::getRelationships() as $key => $value) {
           $promoted_simple_ads->with($key);
        }

        $promoted = $promoted_simple_ads->get()->toArray();*/
        
        if(Redis::exists('shop_ads') && !$request->filters) {
            $data = json_decode(Redis::get('shop_ads'));
            return ['data' => $data];
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ShopAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'category', 'make_id', 'model_id', 'manufacturer', 'code', 'condition', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','custom_model'],

            // set columns to searchIn
            ['id', 'ad_id', 'category', 'make_id', 'model_id', 'manufacturer', 'code', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','custom_model'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'category', 'make_id', 'model_id', 'manufacturer', 'code', 'condition', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link','custom_model'];
                
                
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

                foreach ( ShopAd::getRelationships() as $key => $value) {
                   $query->with($key);
                }
                $query->with(['ad' => function ($query)
                    {
                        $query->with(['images','user']);
                    }
                ]);
            }
        );
        
        /*$data = $data->toArray(); 
            
        array_push($promoted,...$data['data']);
    
        $data['data'] = $promoted;*/
        if(!$request->filters){
            Redis::set('shop_ads',json_encode($data));
        }
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

        $data = ShopAd::whereRaw("ad_id in (SELECT id FROM ads where (ads.title LIKE '%".$filter."%' or ads.description LIKE '%".$filter."%') and type = 'shop')")
                    ->with(['make','model','ad'=> function($query)
                        {
                            $query->with(['images']);
                        },
                    'dealer','dealerShowRoom'])->paginate(25);

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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'market_id' => 'required|string|exists:markets,id',
            'category' => ['required', 'string'],
            'make_id' => 'required|string|exists:makes,id',
            'model_id' => 'nullable|string|exists:models,id',
            'model' => 'nullable|string',
            'manufacturer' => ['required', 'string'],
            'code' => ['nullable', 'string'],
            'condition' => ['required', 'string'],
            'price' => ['required', 'numeric','max:99999999'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email_address' => ['required', 'string'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
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

            $ad = new Ad;
            $ad->slug =  $slug;
            $ad->title =  $request['title'];
            $ad->description =  $request['description'];
            $ad->status =  0;
            $ad->type =  'shop';
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
            Redis::del('shop_ads');
            Redis::del('by_user_'.Auth::user()->id.'_filter_shop');
            $shopAd = new ShopAd;
            $shopAd->ad_id = $ad->id;
            $shopAd->category = $request['category'];
            $shopAd->price_contains_vat = $request['price_contains_vat'];
            $shopAd->make_id = $request['make_id'];
            $shopAd->model_id = $request['model_id'];
            $shopAd->custom_model = $request['model'];
            $shopAd->manufacturer = $request['manufacturer'];
            $shopAd->code = $request['code'];
            $shopAd->condition = $request['condition'];
            $shopAd->price = $request['price'];
            $shopAd->first_name = $request['first_name'];
            $shopAd->last_name = $request['last_name'];
            $shopAd->email_address = $request['email_address'];
            $shopAd->address = $request['address'];
            $shopAd->zip_code = $request['zip_code'];
            $shopAd->dealer_id =  Auth::user()->dealer_id ?? null;
            $shopAd->dealer_show_room_id = $dealer_show_room_id;
            $shopAd->city = $request['city'];
            $shopAd->country = $request['country'];
            $shopAd->latitude = $request['latitude'];
            $shopAd->longitude = $request['longitude'];
            $shopAd->mobile_number = $request['mobile_number'];
            $shopAd->landline_number = $request['landline_number'];
            $shopAd->whatsapp_number = $request['whatsapp_number'];
            $shopAd->youtube_link = $request['youtube_link'];
            $shopAd->save();
            

            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));

            return response()->json([
                'data' => [
                    'ad' => $ad,
                    'shop_ad' =>  $shopAd
                ]
            ], 200);

        } catch (Exception $e) {
            $ad->delete();
            ApiHelper::setError($resource, 0, 500, $e->getMessage().', Line: '.$e->getLine());
            return $this->sendResponse($resource);
        }
    }


    public function search_advanced(Request $request)
    {
        $resource = ApiHelper::resource();

        try {
            
            $shop_ads = new ShopAd;

            if ($request->category) {
                $shop_ads = $shop_ads->whereIn('category',$request->category);
            }
            
            if ($request->make_id) {
                $shop_ads = $shop_ads->where('make_id',$request->make_id);
            }

            if ($request->country) {
                $shop_ads = $shop_ads->where('country',$request->country);
            }

            if ($request->manufacturer) {
                $shop_ads = $shop_ads->where('manufacturer','LIKE','%'.$request->manufacturer.'%');
            }

            if ($request->model) {
                $shop_ads = $shop_ads->where('custom_model','LIKE','%'.$request->model.'%');
            }

            if ($request->condition) {
                $shop_ads = $shop_ads->where('condition',$request->condition);
            }

            if (!is_null($request->from_price) && !is_null($request->to_price)) {
                $shop_ads = $shop_ads->whereBetween('price',[$request->from_price,$request->to_price]);
            }

            foreach (ShopAd::getRelationships() as $key => $value) {
                   $shop_ads->with($key);
            }
            
            return response()->json(['data' => $shop_ads->paginate(25)], 200);

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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'market_id' => ['required', 'string'],
            'category' => ['required', 'string'],
            'make_id' => ['required', 'string'],
            'model_id' => 'nullable|string|exists:models,id',
            'model' => 'nullable|string',
            'manufacturer' => ['required', 'string'],
            'code' => ['nullable', 'string'],
            'condition' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email_address' => ['required', 'string'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'image_ids' => ['nullable', 'array'],
            'eliminated_thumbnail' => ['required', 'boolean'],
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
            Redis::del('shop_ads');
            Redis::del('by_user_'.Auth::user()->id.'_filter_shop');

            $shopAd = ShopAd::where('ad_id',$id)->first();
            $shopAd->category = $request['category'];
            $shopAd->make_id = $request['make_id'];
            $shopAd->price_contains_vat = $request['price_contains_vat'];
            $shopAd->model_id = $request['model_id'];
            $shopAd->custom_model = $request['model'];
            $shopAd->manufacturer = $request['manufacturer'];
            $shopAd->code = $request['code'];
            $shopAd->condition = $request['condition'];
            $shopAd->price = $request['price'];
            $shopAd->first_name = $request['first_name'];
            $shopAd->last_name = $request['last_name'];
            $shopAd->email_address = $request['email_address'];
            $shopAd->address = $request['address'];
            $shopAd->zip_code = $request['zip_code'];
            $shopAd->city = $request['city'];
            $shopAd->country = $request['country'];
            $shopAd->latitude = $request['latitude'];
            $shopAd->longitude = $request['longitude'];
            $shopAd->mobile_number = $request['mobile_number'];
            $shopAd->landline_number = $request['landline_number'];
            $shopAd->whatsapp_number = $request['whatsapp_number'];
            $shopAd->youtube_link = $request['youtube_link'];
            $shopAd->save();
            

            //$user = Auth::user();

            //$user->notify(new \App\Notifications\NewAd($user));

            return response()->json([
                'data' => [
                    'ad' => $ad,
                    'shop_ad' =>  $shopAd
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
     * @param DestroyShopAd $request
     * @param ShopAd $shopAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyShopAd $request, ShopAd $shopAd)
    {
        $shopAd->delete();
        Redis::del('shop_ads');
        Redis::del('by_user_'.Auth::user()->id.'_filter_shop');

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

    public function shopAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','shop')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'shopAd' => function($query)
                        {
                            $query->with(['make','model','ad','dealer','dealerShowRoom']);
                        }
                    ]
                );

        return ['data' => $data->get()];
    }
}
