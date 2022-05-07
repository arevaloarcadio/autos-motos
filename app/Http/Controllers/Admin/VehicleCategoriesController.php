<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VehicleCategory\BulkDestroyVehicleCategory;
use App\Http\Requests\Admin\VehicleCategory\DestroyVehicleCategory;
use App\Http\Requests\Admin\VehicleCategory\IndexVehicleCategory;
use App\Http\Requests\Admin\VehicleCategory\StoreVehicleCategory;
use App\Http\Requests\Admin\VehicleCategory\UpdateVehicleCategory;
use App\Models\VehicleCategory;
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

class VehicleCategoriesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexVehicleCategory $request
     * @return array|Factory|View
     */
    public function index(IndexVehicleCategory $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(VehicleCategory::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'internal_name', 'ad_type'],

            // set columns to searchIn
            ['id', 'internal_name', 'slug', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'internal_name', 'slug', 'ad_type'];
                
                foreach ($columns as $column) {
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
        $this->authorize('admin.vehicle-category.create');

        return view('admin.vehicle-category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreVehicleCategory $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreVehicleCategory $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the VehicleCategory
        $vehicleCategory = VehicleCategory::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/vehicle-categories'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/vehicle-categories');
    }

    /**
     * Display the specified resource.
     *
     * @param VehicleCategory $vehicleCategory
     * @throws AuthorizationException
     * @return void
     */
    public function show(VehicleCategory $vehicleCategory)
    {
        $this->authorize('admin.vehicle-category.show', $vehicleCategory);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param VehicleCategory $vehicleCategory
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(VehicleCategory $vehicleCategory)
    {
        $this->authorize('admin.vehicle-category.edit', $vehicleCategory);


        return view('admin.vehicle-category.edit', [
            'vehicleCategory' => $vehicleCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateVehicleCategory $request
     * @param VehicleCategory $vehicleCategory
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateVehicleCategory $request, VehicleCategory $vehicleCategory)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values VehicleCategory
        $vehicleCategory->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/vehicle-categories'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/vehicle-categories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyVehicleCategory $request
     * @param VehicleCategory $vehicleCategory
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyVehicleCategory $request, VehicleCategory $vehicleCategory)
    {
        $vehicleCategory->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyVehicleCategory $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyVehicleCategory $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    VehicleCategory::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
