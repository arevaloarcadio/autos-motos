<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Make\BulkDestroyMake;
use App\Http\Requests\Admin\Make\DestroyMake;
use App\Http\Requests\Admin\Make\IndexMake;
use App\Http\Requests\Admin\Make\StoreMake;
use App\Http\Requests\Admin\Make\UpdateMake;
use App\Models\Make;
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

class MakesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMake $request
     * @return array|Factory|View
     */
    public function index(IndexMake $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Make::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'is_active', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'slug', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'name', 'is_active', 'ad_type', 'external_id', 'external_updated_at'];
                
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
        $this->authorize('admin.make.create');

        return view('admin.make.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMake $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMake $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Make
        $make = Make::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/makes'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/makes');
    }

    /**
     * Display the specified resource.
     *
     * @param Make $make
     * @throws AuthorizationException
     * @return void
     */
    public function show(Make $make)
    {
        $this->authorize('admin.make.show', $make);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Make $make
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Make $make)
    {
        $this->authorize('admin.make.edit', $make);


        return view('admin.make.edit', [
            'make' => $make,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMake $request
     * @param Make $make
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMake $request, Make $make)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Make
        $make->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/makes'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/makes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMake $request
     * @param Make $make
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMake $request, Make $make)
    {
        $make->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMake $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMake $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Make::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
