<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Option\BulkDestroyOption;
use App\Http\Requests\Admin\Option\DestroyOption;
use App\Http\Requests\Admin\Option\IndexOption;
use App\Http\Requests\Admin\Option\StoreOption;
use App\Http\Requests\Admin\Option\UpdateOption;
use App\Models\Option;
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

class OptionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexOption $request
     * @return array|Factory|View
     */
    public function index(IndexOption $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Option::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'parent_id', 'ad_type', 'external_id', 'external_updated_at'],

            // set columns to searchIn
            ['id', 'name', 'slug', 'parent_id', 'ad_type'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'parent_id', 'ad_type', 'external_id', 'external_updated_at'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }
                    
                foreach (Option::getRelationships() as $key => $value) {
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
        $this->authorize('admin.option.create');

        return view('admin.option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreOption $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreOption $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Option
        $option = Option::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/options'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/options');
    }

    /**
     * Display the specified resource.
     *
     * @param Option $option
     * @throws AuthorizationException
     * @return void
     */
    public function show(Option $option)
    {
        $this->authorize('admin.option.show', $option);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Option $option
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Option $option)
    {
        $this->authorize('admin.option.edit', $option);


        return view('admin.option.edit', [
            'option' => $option,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOption $request
     * @param Option $option
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateOption $request, Option $option)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Option
        $option->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/options'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/options');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyOption $request
     * @param Option $option
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyOption $request, Option $option)
    {
        $option->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyOption $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyOption $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Option::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
