<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Trim\BulkDestroyTrim;
use App\Http\Requests\Admin\Trim\DestroyTrim;
use App\Http\Requests\Admin\Trim\IndexTrim;
use App\Http\Requests\Admin\Trim\StoreTrim;
use App\Http\Requests\Admin\Trim\UpdateTrim;
use App\Models\Trim;
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

class TrimsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTrim $request
     * @return array|Factory|View
     */
    public function index(IndexTrim $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Trim::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'model_id', 'series_id', 'production_year_start', 'production_year_end', 'is_active', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'model_id', 'series_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'model_id', 'series_id', 'production_year_start', 'production_year_end', 'is_active', 'ad_type', 'external_id', 'external_updated_at'];
                
                foreach ($columns as $column) {
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
        $this->authorize('admin.trim.create');

        return view('admin.trim.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTrim $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTrim $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Trim
        $trim = Trim::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/trims'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/trims');
    }

    /**
     * Display the specified resource.
     *
     * @param Trim $trim
     * @throws AuthorizationException
     * @return void
     */
    public function show(Trim $trim)
    {
        $this->authorize('admin.trim.show', $trim);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Trim $trim
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Trim $trim)
    {
        $this->authorize('admin.trim.edit', $trim);


        return view('admin.trim.edit', [
            'trim' => $trim,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTrim $request
     * @param Trim $trim
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTrim $request, Trim $trim)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Trim
        $trim->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/trims'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/trims');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTrim $request
     * @param Trim $trim
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTrim $request, Trim $trim)
    {
        $trim->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTrim $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTrim $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Trim::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
