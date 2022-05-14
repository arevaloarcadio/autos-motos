<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarGeneration\BulkDestroyCarGeneration;
use App\Http\Requests\Admin\CarGeneration\DestroyCarGeneration;
use App\Http\Requests\Admin\CarGeneration\IndexCarGeneration;
use App\Http\Requests\Admin\CarGeneration\StoreCarGeneration;
use App\Http\Requests\Admin\CarGeneration\UpdateCarGeneration;
use App\Models\CarGeneration;
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

class CarGenerationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarGeneration $request
     * @return array|Factory|View
     */
    public function index(IndexCarGeneration $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarGeneration::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'year', 'car_model_id', 'external_id'],

            // set columns to searchIn
            ['id', 'name', 'car_model_id'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'name', 'year', 'car_model_id', 'external_id'];
                
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }

                foreach (CarGeneration::getRelationships() as $key => $value) {
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
        $this->authorize('admin.car-generation.create');

        return view('admin.car-generation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarGeneration $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarGeneration $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarGeneration
        $carGeneration = CarGeneration::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/car-generations'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/car-generations');
    }

    /**
     * Display the specified resource.
     *
     * @param CarGeneration $carGeneration
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarGeneration $carGeneration)
    {
        $this->authorize('admin.car-generation.show', $carGeneration);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarGeneration $carGeneration
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarGeneration $carGeneration)
    {
        $this->authorize('admin.car-generation.edit', $carGeneration);


        return view('admin.car-generation.edit', [
            'carGeneration' => $carGeneration,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarGeneration $request
     * @param CarGeneration $carGeneration
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarGeneration $request, CarGeneration $carGeneration)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarGeneration
        $carGeneration->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-generations'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-generations');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarGeneration $request
     * @param CarGeneration $carGeneration
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarGeneration $request, CarGeneration $carGeneration)
    {
        $carGeneration->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarGeneration $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarGeneration $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarGeneration::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
