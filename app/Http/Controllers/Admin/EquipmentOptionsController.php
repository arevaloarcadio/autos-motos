<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EquipmentOption\BulkDestroyEquipmentOption;
use App\Http\Requests\Admin\EquipmentOption\DestroyEquipmentOption;
use App\Http\Requests\Admin\EquipmentOption\IndexEquipmentOption;
use App\Http\Requests\Admin\EquipmentOption\StoreEquipmentOption;
use App\Http\Requests\Admin\EquipmentOption\UpdateEquipmentOption;
use App\Models\EquipmentOption;
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

class EquipmentOptionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexEquipmentOption $request
     * @return array|Factory|View
     */
    public function index(IndexEquipmentOption $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(EquipmentOption::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['equipment_id', 'option_id', 'is_base', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['equipment_id', 'option_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['equipment_id', 'option_id', 'is_base', 'ad_type', 'external_id', 'external_updated_at'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (EquipmentOption::getRelationships() as $key => $value) {
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
        $this->authorize('admin.equipment-option.create');

        return view('admin.equipment-option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreEquipmentOption $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreEquipmentOption $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the EquipmentOption
        $equipmentOption = EquipmentOption::create($sanitized);

        return ['data' => $equipmentOption];
    }

    /**
     * Display the specified resource.
     *
     * @param EquipmentOption $equipmentOption
     * @throws AuthorizationException
     * @return void
     */
    public function show(EquipmentOption $equipmentOption)
    {
        $this->authorize('admin.equipment-option.show', $equipmentOption);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EquipmentOption $equipmentOption
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(EquipmentOption $equipmentOption)
    {
        $this->authorize('admin.equipment-option.edit', $equipmentOption);


        return view('admin.equipment-option.edit', [
            'equipmentOption' => $equipmentOption,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEquipmentOption $request
     * @param EquipmentOption $equipmentOption
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateEquipmentOption $request, EquipmentOption $equipmentOption)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values EquipmentOption
        $equipmentOption->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/equipment-options'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/equipment-options');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyEquipmentOption $request
     * @param EquipmentOption $equipmentOption
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyEquipmentOption $request, EquipmentOption $equipmentOption)
    {
        $equipmentOption->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyEquipmentOption $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyEquipmentOption $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    EquipmentOption::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
