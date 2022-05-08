<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Specification\BulkDestroySpecification;
use App\Http\Requests\Admin\Specification\DestroySpecification;
use App\Http\Requests\Admin\Specification\IndexSpecification;
use App\Http\Requests\Admin\Specification\StoreSpecification;
use App\Http\Requests\Admin\Specification\UpdateSpecification;
use App\Models\Specification;
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

class SpecificationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSpecification $request
     * @return array|Factory|View
     */
    public function index(IndexSpecification $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Specification::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'parent_id', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'slug', 'parent_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'parent_id', 'ad_type', 'external_id', 'external_updated_at'];
                
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
        $this->authorize('admin.specification.create');

        return view('admin.specification.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSpecification $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreSpecification $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Specification
        $specification = Specification::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/specifications'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/specifications');
    }

    /**
     * Display the specified resource.
     *
     * @param Specification $specification
     * @throws AuthorizationException
     * @return void
     */
    public function show(Specification $specification)
    {
        $this->authorize('admin.specification.show', $specification);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Specification $specification
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Specification $specification)
    {
        $this->authorize('admin.specification.edit', $specification);


        return view('admin.specification.edit', [
            'specification' => $specification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSpecification $request
     * @param Specification $specification
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateSpecification $request, Specification $specification)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Specification
        $specification->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/specifications'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/specifications');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroySpecification $request
     * @param Specification $specification
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroySpecification $request, Specification $specification)
    {
        $specification->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroySpecification $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroySpecification $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Specification::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
