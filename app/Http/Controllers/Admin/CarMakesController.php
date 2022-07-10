<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarMake\BulkDestroyCarMake;
use App\Http\Requests\Admin\CarMake\DestroyCarMake;
use App\Http\Requests\Admin\CarMake\IndexCarMake;
use App\Http\Requests\Admin\CarMake\StoreCarMake;
use App\Http\Requests\Admin\CarMake\UpdateCarMake;
use App\Models\CarMake;
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

class CarMakesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCarMake $request
     * @return array|Factory|View
     */
    public function index(IndexCarMake $request)
    {
        if ($request->all) {
            
            $query = CarMake::query();

            $columns =['id', 'name', 'external_id', 'is_active'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (CarMake::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CarMake::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'external_id', 'is_active'],

            // set columns to searchIn
            ['id', 'name', 'slug'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'name', 'external_id', 'is_active'];
                
if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

                foreach (CarMake::getRelationships() as $key => $value) {
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
        $this->authorize('admin.car-make.create');

        return view('admin.car-make.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCarMake $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCarMake $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CarMake
        $carMake = CarMake::create($sanitized);

        return ['data' => $carMake];
    }

    /**
     * Display the specified resource.
     *
     * @param CarMake $carMake
     * @throws AuthorizationException
     * @return void
     */
    public function show(CarMake $carMake)
    {
        $this->authorize('admin.car-make.show', $carMake);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CarMake $carMake
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CarMake $carMake)
    {
        $this->authorize('admin.car-make.edit', $carMake);


        return view('admin.car-make.edit', [
            'carMake' => $carMake,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarMake $request
     * @param CarMake $carMake
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCarMake $request, CarMake $carMake)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CarMake
        $carMake->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/car-makes'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/car-makes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCarMake $request
     * @param CarMake $carMake
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCarMake $request, CarMake $carMake)
    {
        $carMake->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCarMake $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCarMake $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CarMake::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
