<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubCharacteristic\BulkDestroySubCharacteristic;
use App\Http\Requests\Admin\SubCharacteristic\DestroySubCharacteristic;
use App\Http\Requests\Admin\SubCharacteristic\IndexSubCharacteristic;
use App\Http\Requests\Admin\SubCharacteristic\StoreSubCharacteristic;
use App\Http\Requests\Admin\SubCharacteristic\UpdateSubCharacteristic;
use App\Models\SubCharacteristic;
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

class SubCharacteristicsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSubCharacteristic $request
     * @return array|Factory|View
     */
    public function index(IndexSubCharacteristic $request)
    {   
        if ($request->all) {
            
            $query = SubCharacteristic::query();

            $columns = ['id', 'name', 'characteristic_id'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (SubCharacteristic::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(SubCharacteristic::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'characteristic_id'],

            // set columns to searchIn
            ['id', 'name', 'characteristic_id']
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
        $this->authorize('admin.sub-characteristic.create');

        return view('admin.sub-characteristic.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSubCharacteristic $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreSubCharacteristic $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the SubCharacteristic
        $sub_characteristics = [];
        
        foreach ($sanitized['sub_characteristics'] as $key => $sub_characteristic) {
            $sub_characteristics[$key] = SubCharacteristic::create($sub_characteristic);
        }

        return ['data' => $sub_characteristics];
    }

    /**
     * Display the specified resource.
     *
     * @param SubCharacteristic $subCharacteristic
     * @throws AuthorizationException
     * @return void
     */
    public function show(SubCharacteristic $subCharacteristic)
    {
        $this->authorize('admin.sub-characteristic.show', $subCharacteristic);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SubCharacteristic $subCharacteristic
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(SubCharacteristic $subCharacteristic)
    {
        $this->authorize('admin.sub-characteristic.edit', $subCharacteristic);


        return view('admin.sub-characteristic.edit', [
            'subCharacteristic' => $subCharacteristic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSubCharacteristic $request
     * @param SubCharacteristic $subCharacteristic
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateSubCharacteristic $request, SubCharacteristic $subCharacteristic)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values SubCharacteristic
        $subCharacteristic->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/sub-characteristics'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/sub-characteristics');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroySubCharacteristic $request
     * @param SubCharacteristic $subCharacteristic
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroySubCharacteristic $request, SubCharacteristic $subCharacteristic)
    {
        $subCharacteristic->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroySubCharacteristic $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroySubCharacteristic $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    SubCharacteristic::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
