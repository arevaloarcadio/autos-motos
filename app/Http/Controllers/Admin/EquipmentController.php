<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Equipment\BulkDestroyEquipment;
use App\Http\Requests\Admin\Equipment\DestroyEquipment;
use App\Http\Requests\Admin\Equipment\IndexEquipment;
use App\Http\Requests\Admin\Equipment\StoreEquipment;
use App\Http\Requests\Admin\Equipment\UpdateEquipment;
use App\Models\Equipment;
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

class EquipmentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexEquipment $request
     * @return array|Factory|View
     */
    public function index(IndexEquipment $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Equipment::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'trim_id', 'year', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'trim_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'trim_id', 'year', 'ad_type', 'external_id', 'external_updated_at'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (Equipment::getRelationships() as $key => $value) {
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
        $this->authorize('admin.equipment.create');

        return view('admin.equipment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreEquipment $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreEquipment $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Equipment
        $equipment = Equipment::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/equipment'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/equipment');
    }

    /**
     * Display the specified resource.
     *
     * @param Equipment $equipment
     * @throws AuthorizationException
     * @return void
     */
    public function show(Equipment $equipment)
    {
        $this->authorize('admin.equipment.show', $equipment);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Equipment $equipment
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Equipment $equipment)
    {
        $this->authorize('admin.equipment.edit', $equipment);


        return view('admin.equipment.edit', [
            'equipment' => $equipment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEquipment $request
     * @param Equipment $equipment
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateEquipment $request, Equipment $equipment)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Equipment
        $equipment->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/equipment'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/equipment');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyEquipment $request
     * @param Equipment $equipment
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyEquipment $request, Equipment $equipment)
    {
        $equipment->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyEquipment $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyEquipment $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Equipment::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
