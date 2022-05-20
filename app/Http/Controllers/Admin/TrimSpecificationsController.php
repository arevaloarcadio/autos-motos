<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrimSpecification\BulkDestroyTrimSpecification;
use App\Http\Requests\Admin\TrimSpecification\DestroyTrimSpecification;
use App\Http\Requests\Admin\TrimSpecification\IndexTrimSpecification;
use App\Http\Requests\Admin\TrimSpecification\StoreTrimSpecification;
use App\Http\Requests\Admin\TrimSpecification\UpdateTrimSpecification;
use App\Models\TrimSpecification;
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

class TrimSpecificationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTrimSpecification $request
     * @return array|Factory|View
     */
    public function index(IndexTrimSpecification $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TrimSpecification::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'trim_id', 'specification_id', 'value', 'unit', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'trim_id', 'specification_id', 'value', 'unit', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'trim_id', 'specification_id', 'value', 'unit', 'ad_type', 'external_id', 'external_updated_at'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (TrimSpecification::getRelationships() as $key => $value) {
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
        $this->authorize('admin.trim-specification.create');

        return view('admin.trim-specification.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTrimSpecification $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTrimSpecification $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the TrimSpecification
        $trimSpecification = TrimSpecification::create($sanitized);

        return ['data' => $trimSpecification];
    }

    /**
     * Display the specified resource.
     *
     * @param TrimSpecification $trimSpecification
     * @throws AuthorizationException
     * @return void
     */
    public function show(TrimSpecification $trimSpecification)
    {
        $this->authorize('admin.trim-specification.show', $trimSpecification);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TrimSpecification $trimSpecification
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TrimSpecification $trimSpecification)
    {
        $this->authorize('admin.trim-specification.edit', $trimSpecification);


        return view('admin.trim-specification.edit', [
            'trimSpecification' => $trimSpecification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTrimSpecification $request
     * @param TrimSpecification $trimSpecification
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTrimSpecification $request, TrimSpecification $trimSpecification)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values TrimSpecification
        $trimSpecification->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/trim-specifications'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/trim-specifications');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTrimSpecification $request
     * @param TrimSpecification $trimSpecification
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTrimSpecification $request, TrimSpecification $trimSpecification)
    {
        $trimSpecification->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTrimSpecification $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTrimSpecification $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    TrimSpecification::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
