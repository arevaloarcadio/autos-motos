<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarFuelType\BulkDestroyCarFuelType;
use App\Http\Requests\Admin\CarFuelType\DestroyCarFuelType;
use App\Http\Requests\Admin\CarFuelType\IndexCarFuelType;
use App\Http\Requests\Admin\CarFuelType\StoreCarFuelType;
use App\Http\Requests\Admin\CarFuelType\UpdateCarFuelType;
use App\Models\CarFuelType;
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

class CarFuelTypesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarFuelType $request
     * @return array|Factory|View
     */
    public function index(IndexCarFuelType $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarFuelType::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'internal_name', 'external_name', 'ad_type'],

            // set columns to searchIn
            ['id', 'internal_name', 'slug', 'external_name', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'internal_name', 'slug', 'external_name', 'ad_type'];
                
                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (CarFuelType::getRelationships() as $key => $value) {
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
        $this->authorize('admin.car-fuel-type.create');

        return view('admin.car-fuel-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarFuelType $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarFuelType $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarFuelType
        $carFuelType = CarFuelType::create($sanitized);

        return ['data' => $carFuelType];
    }

    /**
     * Display the specified resource.
     *
     * @param CarFuelType $carFuelType
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarFuelType $carFuelType)
    {
        $this->authorize('admin.car-fuel-type.show', $carFuelType);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarFuelType $carFuelType
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarFuelType $carFuelType)
    {
        $this->authorize('admin.car-fuel-type.edit', $carFuelType);


        return view('admin.car-fuel-type.edit', [
            'carFuelType' => $carFuelType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarFuelType $request
     * @param CarFuelType $carFuelType
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarFuelType $request, CarFuelType $carFuelType)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarFuelType
        $carFuelType->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-fuel-types'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-fuel-types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarFuelType $request
     * @param CarFuelType $carFuelType
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarFuelType $request, CarFuelType $carFuelType)
    {
        $carFuelType->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarFuelType $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarFuelType $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarFuelType::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
