<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShopAd\BulkDestroyShopAd;
use App\Http\Requests\Admin\ShopAd\DestroyShopAd;
use App\Http\Requests\Admin\ShopAd\IndexShopAd;
use App\Http\Requests\Admin\ShopAd\StoreShopAd;
use App\Http\Requests\Admin\ShopAd\UpdateShopAd;
use App\Models\ShopAd;
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

class ShopAdsController extends Controller
{

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
           
                
            foreach ($columns as $column) {
                if ($request->filters) {
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
                
                foreach ($columns as $column) {
                        if ($request->filters) {
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

        // Store the ShopAd
        $shopAd = ShopAd::create($sanitized);

        return ['data' => $shopAd];
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
}
