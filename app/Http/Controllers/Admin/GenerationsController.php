<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Generation\BulkDestroyGeneration;
use App\Http\Requests\Admin\Generation\DestroyGeneration;
use App\Http\Requests\Admin\Generation\IndexGeneration;
use App\Http\Requests\Admin\Generation\StoreGeneration;
use App\Http\Requests\Admin\Generation\UpdateGeneration;
use App\Models\Generation;
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

class GenerationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexGeneration $request
     * @return array|Factory|View
     */
    public function index(IndexGeneration $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Generation::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'model_id', 'year_begin', 'year_end', 'is_active', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'model_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'name', 'model_id', 'year_begin', 'year_end', 'is_active', 'ad_type', 'external_id', 'external_updated_at'];
                
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }

                foreach (Generation::getRelationships() as $key => $value) {
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
        $this->authorize('admin.generation.create');

        return view('admin.generation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreGeneration $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreGeneration $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Generation
        $generation = Generation::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/generations'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/generations');
    }

    /**
     * Display the specified resource.
     *
     * @param Generation $generation
     * @throws AuthorizationException
     * @return void
     */
    public function show(Generation $generation)
    {
        $this->authorize('admin.generation.show', $generation);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Generation $generation
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Generation $generation)
    {
        $this->authorize('admin.generation.edit', $generation);


        return view('admin.generation.edit', [
            'generation' => $generation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateGeneration $request
     * @param Generation $generation
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateGeneration $request, Generation $generation)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Generation
        $generation->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/generations'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/generations');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyGeneration $request
     * @param Generation $generation
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyGeneration $request, Generation $generation)
    {
        $generation->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyGeneration $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyGeneration $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Generation::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
