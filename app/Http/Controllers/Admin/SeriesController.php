<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Series\BulkDestroySeries;
use App\Http\Requests\Admin\Series\DestroySeries;
use App\Http\Requests\Admin\Series\IndexSeries;
use App\Http\Requests\Admin\Series\StoreSeries;
use App\Http\Requests\Admin\Series\UpdateSeries;
use App\Models\Series;
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

class SeriesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSeries $request
     * @return array|Factory|View
     */
    public function index(IndexSeries $request)
    {
        if ($request->all) {
            
            $query = Series::query();

            $columns =  ['id', 'name', 'model_id', 'generation_id', 'is_active', 'ad_type', 'external_id', 'external_updated_at'];
                
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (Series::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Series::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'model_id', 'generation_id', 'is_active', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'model_id', 'generation_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'model_id', 'generation_id', 'is_active', 'ad_type', 'external_id', 'external_updated_at'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (Series::getRelationships() as $key => $value) {
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
        $this->authorize('admin.series.create');

        return view('admin.series.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSeries $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreSeries $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Series
        $series = Series::create($sanitized);

        return ['data' => $series];
    }

    /**
     * Display the specified resource.
     *
     * @param Series $series
     * @throws AuthorizationException
     * @return void
     */
    public function show(Series $series)
    {
        $this->authorize('admin.series.show', $series);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Series $series
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Series $series)
    {
        $this->authorize('admin.series.edit', $series);


        return view('admin.series.edit', [
            'series' => $series,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSeries $request
     * @param Series $series
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateSeries $request, Series $series)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Series
        $series->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/series'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/series');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroySeries $request
     * @param Series $series
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroySeries $request, Series $series)
    {
        $series->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroySeries $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroySeries $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Series::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
