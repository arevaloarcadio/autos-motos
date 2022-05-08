<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarModel\BulkDestroyCarModel;
use App\Http\Requests\Admin\CarModel\DestroyCarModel;
use App\Http\Requests\Admin\CarModel\IndexCarModel;
use App\Http\Requests\Admin\CarModel\StoreCarModel;
use App\Http\Requests\Admin\CarModel\UpdateCarModel;
use App\Models\CarModel;
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

class CarModelsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarModel $request
     * @return array|Factory|View
     */
    public function index(IndexCarModel $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarModel::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'car_make_id', 'external_id'],

            // set columns to searchIn
            ['id', 'name', 'slug', 'car_make_id'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'name', 'slug', 'car_make_id'];
                
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
        $this->authorize('admin.car-model.create');

        return view('admin.car-model.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarModel $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarModel $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarModel
        $carModel = CarModel::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/car-models'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/car-models');
    }

    /**
     * Display the specified resource.
     *
     * @param CarModel $carModel
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarModel $carModel)
    {
        $this->authorize('admin.car-model.show', $carModel);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarModel $carModel
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarModel $carModel)
    {
        $this->authorize('admin.car-model.edit', $carModel);


        return view('admin.car-model.edit', [
            'carModel' => $carModel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarModel $request
     * @param CarModel $carModel
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarModel $request, CarModel $carModel)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarModel
        $carModel->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-models'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-models');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarModel $request
     * @param CarModel $carModel
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarModel $request, CarModel $carModel)
    {
        $carModel->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarModel $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarModel $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarModel::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
