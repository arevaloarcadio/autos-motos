<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdModel\BulkDestroyAdModel;
use App\Http\Requests\Admin\AdModel\DestroyAdModel;
use App\Http\Requests\Admin\AdModel\IndexAdModel;
use App\Http\Requests\Admin\AdModel\StoreAdModel;
use App\Http\Requests\Admin\AdModel\UpdateAdModel;
use App\Models\AdModel;
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

class AdModelsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdModel $request
     * @return array|Factory|View
     */
    public function index(IndexAdModel $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdModel::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'ad_type', 'parent_id', 'ad_make_id'],

            // set columns to searchIn
            ['id', 'name', 'slug', 'ad_type', 'parent_id', 'ad_make_id'],

        function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'slug', 'ad_type', 'parent_id', 'ad_make_id'];

                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (AdModel::getRelationships() as $key => $value) {
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
        $this->authorize('admin.ad-model.create');

        return view('admin.ad-model.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdModel $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdModel $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AdModel
        $adModel = AdModel::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/ad-models'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/ad-models');
    }

    /**
     * Display the specified resource.
     *
     * @param AdModel $adModel
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdModel $adModel)
    {
        $this->authorize('admin.ad-model.show', $adModel);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdModel $adModel
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdModel $adModel)
    {
        $this->authorize('admin.ad-model.edit', $adModel);


        return view('admin.ad-model.edit', [
            'adModel' => $adModel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdModel $request
     * @param AdModel $adModel
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdModel $request, AdModel $adModel)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AdModel
        $adModel->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ad-models'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ad-models');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdModel $request
     * @param AdModel $adModel
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdModel $request, AdModel $adModel)
    {
        $adModel->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAdModel $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAdModel $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AdModel::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
