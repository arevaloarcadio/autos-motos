<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdSubCharacteristic\BulkDestroyAdSubCharacteristic;
use App\Http\Requests\Admin\AdSubCharacteristic\DestroyAdSubCharacteristic;
use App\Http\Requests\Admin\AdSubCharacteristic\IndexAdSubCharacteristic;
use App\Http\Requests\Admin\AdSubCharacteristic\StoreAdSubCharacteristic;
use App\Http\Requests\Admin\AdSubCharacteristic\UpdateAdSubCharacteristic;
use App\Models\AdSubCharacteristic;
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

class AdSubCharacteristicsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdSubCharacteristic $request
     * @return array|Factory|View
     */
    public function index(IndexAdSubCharacteristic $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdSubCharacteristic::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'sub_characteristic_id'],

            // set columns to searchIn
            ['id', 'ad_id', 'sub_characteristic_id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.ad-sub-characteristic.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.ad-sub-characteristic.create');

        return view('admin.ad-sub-characteristic.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdSubCharacteristic $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdSubCharacteristic $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AdSubCharacteristic
        $adSubCharacteristic = AdSubCharacteristic::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/ad-sub-characteristics'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/ad-sub-characteristics');
    }

    /**
     * Display the specified resource.
     *
     * @param AdSubCharacteristic $adSubCharacteristic
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdSubCharacteristic $adSubCharacteristic)
    {
        $this->authorize('admin.ad-sub-characteristic.show', $adSubCharacteristic);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdSubCharacteristic $adSubCharacteristic
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdSubCharacteristic $adSubCharacteristic)
    {
        $this->authorize('admin.ad-sub-characteristic.edit', $adSubCharacteristic);


        return view('admin.ad-sub-characteristic.edit', [
            'adSubCharacteristic' => $adSubCharacteristic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdSubCharacteristic $request
     * @param AdSubCharacteristic $adSubCharacteristic
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdSubCharacteristic $request, AdSubCharacteristic $adSubCharacteristic)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AdSubCharacteristic
        $adSubCharacteristic->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ad-sub-characteristics'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ad-sub-characteristics');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdSubCharacteristic $request
     * @param AdSubCharacteristic $adSubCharacteristic
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdSubCharacteristic $request, AdSubCharacteristic $adSubCharacteristic)
    {
        $adSubCharacteristic->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAdSubCharacteristic $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAdSubCharacteristic $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AdSubCharacteristic::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
