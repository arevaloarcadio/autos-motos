<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Market\BulkDestroyMarket;
use App\Http\Requests\Admin\Market\DestroyMarket;
use App\Http\Requests\Admin\Market\IndexMarket;
use App\Http\Requests\Admin\Market\StoreMarket;
use App\Http\Requests\Admin\Market\UpdateMarket;
use App\Models\Market;
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

class MarketsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMarket $request
     * @return array|Factory|View
     */
    public function index(IndexMarket $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Market::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'internal_name', 'domain', 'default_locale_id', 'icon', 'mobile_number', 'whatsapp_number', 'email_address', 'order_index'],

            // set columns to searchIn
            ['id', 'internal_name', 'slug', 'domain', 'default_locale_id', 'icon', 'mobile_number', 'whatsapp_number', 'email_address'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'internal_name', 'slug', 'domain', 'default_locale_id', 'icon', 'mobile_number', 'whatsapp_number', 'email_address'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (Market::getRelationships() as $key => $value) {
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
        $this->authorize('admin.market.create');

        return view('admin.market.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMarket $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMarket $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Market
        $market = Market::create($sanitized);

        return ['data' => $market];
    }

    /**
     * Display the specified resource.
     *
     * @param Market $market
     * @throws AuthorizationException
     * @return void
     */
    public function show(Market $market)
    {
        $this->authorize('admin.market.show', $market);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Market $market
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Market $market)
    {
        $this->authorize('admin.market.edit', $market);


        return view('admin.market.edit', [
            'market' => $market,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMarket $request
     * @param Market $market
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMarket $request, Market $market)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Market
        $market->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/markets'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/markets');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMarket $request
     * @param Market $market
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMarket $request, Market $market)
    {
        $market->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMarket $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMarket $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Market::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
