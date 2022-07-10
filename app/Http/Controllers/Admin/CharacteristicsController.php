<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Characteristic\BulkDestroyCharacteristic;
use App\Http\Requests\Admin\Characteristic\DestroyCharacteristic;
use App\Http\Requests\Admin\Characteristic\IndexCharacteristic;
use App\Http\Requests\Admin\Characteristic\StoreCharacteristic;
use App\Http\Requests\Admin\Characteristic\UpdateCharacteristic;
use App\Models\Characteristic;
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

class CharacteristicsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCharacteristic $request
     * @return array|Factory|View
     */
    public function index(IndexCharacteristic $request)
    {
        if ($request->all) {
            
            $query = Characteristic::query();

            $columns = ['id', 'name'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (Characteristic::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Characteristic::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name'],

            // set columns to searchIn
            ['id', 'name'],
            function ($query) use ($request) {
                        
                $columns =   ['id', 'name'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (Characteristic::getRelationships() as $key => $value) {
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
        $this->authorize('admin.characteristic.create');

        return view('admin.characteristic.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCharacteristic $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCharacteristic $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Characteristic
        $characteristic = Characteristic::create($sanitized);

        return ['data' => $characteristic];
    }
    /**
     * Display the specified resource.
     *
     * @param Characteristic $characteristic
     * @throws AuthorizationException
     * @return void
     */
    public function show(Characteristic $characteristic)
    {
        $this->authorize('admin.characteristic.show', $characteristic);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Characteristic $characteristic
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Characteristic $characteristic)
    {
        $this->authorize('admin.characteristic.edit', $characteristic);


        return view('admin.characteristic.edit', [
            'characteristic' => $characteristic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCharacteristic $request
     * @param Characteristic $characteristic
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCharacteristic $request, Characteristic $characteristic)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Characteristic
        $characteristic->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/characteristics'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/characteristics');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCharacteristic $request
     * @param Characteristic $characteristic
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCharacteristic $request, Characteristic $characteristic)
    {
        $characteristic->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCharacteristic $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCharacteristic $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Characteristic::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
