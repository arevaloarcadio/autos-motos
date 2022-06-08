<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarTransmissionType\BulkDestroyCarTransmissionType;
use App\Http\Requests\Admin\CarTransmissionType\DestroyCarTransmissionType;
use App\Http\Requests\Admin\CarTransmissionType\IndexCarTransmissionType;
use App\Http\Requests\Admin\CarTransmissionType\StoreCarTransmissionType;
use App\Http\Requests\Admin\CarTransmissionType\UpdateCarTransmissionType;
use App\Models\CarTransmissionType;
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

class CarTransmissionTypesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarTransmissionType $request
     * @return array|Factory|View
     */
    public function index(IndexCarTransmissionType $request)
    {
        if ($request->all) {
            
            $query = CarTransmissionType::query();

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

            foreach (CarTransmissionType::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarTransmissionType::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'internal_name', 'external_name', 'ad_type'],

            // set columns to searchIn
            ['id', 'internal_name', 'slug', 'external_name', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'internal_name', 'slug', 'external_name', 'ad_type'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (CarTransmissionType::getRelationships() as $key => $value) {
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
        $this->authorize('admin.car-transmission-type.create');

        return view('admin.car-transmission-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarTransmissionType $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarTransmissionType $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarTransmissionType
        $carTransmissionType = CarTransmissionType::create($sanitized);

        return ['data' => $carTransmissionType];
    }

    /**
     * Display the specified resource.
     *
     * @param CarTransmissionType $carTransmissionType
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarTransmissionType $carTransmissionType)
    {
        $this->authorize('admin.car-transmission-type.show', $carTransmissionType);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarTransmissionType $carTransmissionType
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarTransmissionType $carTransmissionType)
    {
        $this->authorize('admin.car-transmission-type.edit', $carTransmissionType);


        return view('admin.car-transmission-type.edit', [
            'carTransmissionType' => $carTransmissionType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarTransmissionType $request
     * @param CarTransmissionType $carTransmissionType
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarTransmissionType $request, CarTransmissionType $carTransmissionType)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarTransmissionType
        $carTransmissionType->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-transmission-types'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-transmission-types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarTransmissionType $request
     * @param CarTransmissionType $carTransmissionType
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarTransmissionType $request, CarTransmissionType $carTransmissionType)
    {
        $carTransmissionType->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarTransmissionType $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarTransmissionType $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarTransmissionType::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
