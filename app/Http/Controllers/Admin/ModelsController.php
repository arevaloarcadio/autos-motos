<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Model\BulkDestroyModel;
use App\Http\Requests\Admin\Model\DestroyModel;
use App\Http\Requests\Admin\Model\IndexModel;
use App\Http\Requests\Admin\Model\StoreModel;
use App\Http\Requests\Admin\Model\UpdateModel;
use App\Models\Model;
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

class ModelsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexModel $request
     * @return array|Factory|View
     */
    public function index(IndexModel $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Model::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'make_id', 'is_active', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'slug', 'make_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'make_id', 'is_active', 'ad_type', 'external_id', 'external_updated_at'];
                
                if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
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
        $this->authorize('admin.model.create');

        return view('admin.model.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreModel $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreModel $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Model
        $model = Model::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/models'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/models');
    }

    /**
     * Display the specified resource.
     *
     * @param Model $model
     * @throws AuthorizationException
     * @return void
     */
    public function show(Model $model)
    {
        $this->authorize('admin.model.show', $model);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Model $model
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Model $model)
    {
        $this->authorize('admin.model.edit', $model);


        return view('admin.model.edit', [
            'model' => $model,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateModel $request
     * @param Model $model
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateModel $request, Model $model)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Model
        $model->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/models'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/models');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyModel $request
     * @param Model $model
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyModel $request, Model $model)
    {
        $model->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyModel $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyModel $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Model::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
