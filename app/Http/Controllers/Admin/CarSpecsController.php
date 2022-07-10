<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarSpec\BulkDestroyCarSpec;
use App\Http\Requests\Admin\CarSpec\DestroyCarSpec;
use App\Http\Requests\Admin\CarSpec\IndexCarSpec;
use App\Http\Requests\Admin\CarSpec\StoreCarSpec;
use App\Http\Requests\Admin\CarSpec\UpdateCarSpec;
use App\Models\CarSpec;
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

class CarSpecsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarSpec $request
     * @return array|Factory|View
     */
    public function index(IndexCarSpec $request)
    {
        if ($request->all) {
            
            $query = CarSpec::query();

            $columns = ['id', 'car_make_id', 'car_model_id', 'car_generation_id', 'car_body_type_id', 'engine', 'doors', 'doors_min', 'doors_max', 'power_hp', 'power_rpm', 'power_rpm_min', 'power_rpm_max', 'engine_displacement', 'production_start_year', 'production_end_year', 'car_fuel_type_id', 'car_transmission_type_id', 'gears', 'car_wheel_drive_type_id', 'battery_capacity', 'electric_power_hp', 'electric_power_rpm', 'electric_power_rpm_min', 'electric_power_rpm_max', 'external_id', 'last_external_update'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (CarSpec::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarSpec::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'car_make_id', 'car_model_id', 'car_generation_id', 'car_body_type_id', 'engine', 'doors', 'doors_min', 'doors_max', 'power_hp', 'power_rpm', 'power_rpm_min', 'power_rpm_max', 'engine_displacement', 'production_start_year', 'production_end_year', 'car_fuel_type_id', 'car_transmission_type_id', 'gears', 'car_wheel_drive_type_id', 'battery_capacity', 'electric_power_hp', 'electric_power_rpm', 'electric_power_rpm_min', 'electric_power_rpm_max', 'external_id', 'last_external_update'],

            // set columns to searchIn
            ['id', 'car_make_id', 'car_model_id', 'car_generation_id', 'car_body_type_id', 'engine', 'doors', 'power_rpm', 'car_fuel_type_id', 'car_transmission_type_id', 'car_wheel_drive_type_id', 'electric_power_rpm'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'car_make_id', 'car_model_id', 'car_generation_id', 'car_body_type_id', 'engine', 'doors', 'doors_min', 'doors_max', 'power_hp', 'power_rpm', 'power_rpm_min', 'power_rpm_max', 'engine_displacement', 'production_start_year', 'production_end_year', 'car_fuel_type_id', 'car_transmission_type_id', 'gears', 'car_wheel_drive_type_id', 'battery_capacity', 'electric_power_hp', 'electric_power_rpm', 'electric_power_rpm_min', 'electric_power_rpm_max', 'external_id', 'last_external_update'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (CarSpec::getRelationships() as $key => $value) {
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
        $this->authorize('admin.car-spec.create');

        return view('admin.car-spec.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarSpec $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarSpec $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarSpec
        $carSpec = CarSpec::create($sanitized);

        return ['data' => $carSpec];
    }

    /**
     * Display the specified resource.
     *
     * @param CarSpec $carSpec
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarSpec $carSpec)
    {
        $this->authorize('admin.car-spec.show', $carSpec);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarSpec $carSpec
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarSpec $carSpec)
    {
        $this->authorize('admin.car-spec.edit', $carSpec);


        return view('admin.car-spec.edit', [
            'carSpec' => $carSpec,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarSpec $request
     * @param CarSpec $carSpec
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarSpec $request, CarSpec $carSpec)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarSpec
        $carSpec->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-specs'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-specs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarSpec $request
     * @param CarSpec $carSpec
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarSpec $request, CarSpec $carSpec)
    {
        $carSpec->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarSpec $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarSpec $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarSpec::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
