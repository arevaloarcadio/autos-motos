<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarWheelDriveType\BulkDestroyCarWheelDriveType;
use App\Http\Requests\Admin\CarWheelDriveType\DestroyCarWheelDriveType;
use App\Http\Requests\Admin\CarWheelDriveType\IndexCarWheelDriveType;
use App\Http\Requests\Admin\CarWheelDriveType\StoreCarWheelDriveType;
use App\Http\Requests\Admin\CarWheelDriveType\UpdateCarWheelDriveType;
use App\Models\CarWheelDriveType;
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

class CarWheelDriveTypesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarWheelDriveType $request
     * @return array|Factory|View
     */
    public function index(IndexCarWheelDriveType $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarWheelDriveType::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'internal_name', 'external_name', 'ad_type'],

            // set columns to searchIn
            ['id', 'internal_name', 'slug', 'external_name', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'internal_name', 'slug', 'external_name', 'ad_type'];
                
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
        $this->authorize('admin.car-wheel-drive-type.create');

        return view('admin.car-wheel-drive-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarWheelDriveType $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarWheelDriveType $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarWheelDriveType
        $carWheelDriveType = CarWheelDriveType::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/car-wheel-drive-types'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/car-wheel-drive-types');
    }

    /**
     * Display the specified resource.
     *
     * @param CarWheelDriveType $carWheelDriveType
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarWheelDriveType $carWheelDriveType)
    {
        $this->authorize('admin.car-wheel-drive-type.show', $carWheelDriveType);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarWheelDriveType $carWheelDriveType
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarWheelDriveType $carWheelDriveType)
    {
        $this->authorize('admin.car-wheel-drive-type.edit', $carWheelDriveType);


        return view('admin.car-wheel-drive-type.edit', [
            'carWheelDriveType' => $carWheelDriveType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarWheelDriveType $request
     * @param CarWheelDriveType $carWheelDriveType
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarWheelDriveType $request, CarWheelDriveType $carWheelDriveType)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarWheelDriveType
        $carWheelDriveType->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-wheel-drive-types'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-wheel-drive-types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarWheelDriveType $request
     * @param CarWheelDriveType $carWheelDriveType
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarWheelDriveType $request, CarWheelDriveType $carWheelDriveType)
    {
        $carWheelDriveType->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarWheelDriveType $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarWheelDriveType $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarWheelDriveType::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
