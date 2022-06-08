<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarBodyType\BulkDestroyCarBodyType;
use App\Http\Requests\Admin\CarBodyType\DestroyCarBodyType;
use App\Http\Requests\Admin\CarBodyType\IndexCarBodyType;
use App\Http\Requests\Admin\CarBodyType\StoreCarBodyType;
use App\Http\Requests\Admin\CarBodyType\UpdateCarBodyType;
use App\Models\CarBodyType;
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

class CarBodyTypesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarBodyType $request
     * @return array|Factory|View
     */
    public function index(IndexCarBodyType $request)
    {
        if ($request->all) {
            
            $query = CarBodyType::query();

            $columns = ['id', 'internal_name', 'slug', 'icon_url', 'external_name', 'ad_type'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (CarBodyType::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarBodyType::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'internal_name', 'icon_url', 'external_name', 'ad_type'],

            // set columns to searchIn
            ['id', 'internal_name', 'slug', 'icon_url', 'external_name', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'internal_name', 'slug', 'icon_url', 'external_name', 'ad_type'];
                
                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (CarBodyType::getRelationships() as $key => $value) {
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
        $this->authorize('admin.car-body-type.create');

        return view('admin.car-body-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarBodyType $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarBodyType $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarBodyType
        $carBodyType = CarBodyType::create($sanitized);

        return ['data' => $carBodyType];
    }

    /**
     * Display the specified resource.
     *
     * @param CarBodyType $carBodyType
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarBodyType $carBodyType)
    {
        $this->authorize('admin.car-body-type.show', $carBodyType);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarBodyType $carBodyType
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarBodyType $carBodyType)
    {
        $this->authorize('admin.car-body-type.edit', $carBodyType);


        return view('admin.car-body-type.edit', [
            'carBodyType' => $carBodyType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarBodyType $request
     * @param CarBodyType $carBodyType
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarBodyType $request, CarBodyType $carBodyType)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarBodyType
        $carBodyType->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-body-types'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-body-types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarBodyType $request
     * @param CarBodyType $carBodyType
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarBodyType $request, CarBodyType $carBodyType)
    {
        $carBodyType->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarBodyType $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarBodyType $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarBodyType::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
