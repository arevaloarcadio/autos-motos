<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AutoAdOption\BulkDestroyAutoAdOption;
use App\Http\Requests\Admin\AutoAdOption\DestroyAutoAdOption;
use App\Http\Requests\Admin\AutoAdOption\IndexAutoAdOption;
use App\Http\Requests\Admin\AutoAdOption\StoreAutoAdOption;
use App\Http\Requests\Admin\AutoAdOption\UpdateAutoAdOption;
use App\Models\AutoAdOption;
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

class AutoAdOptionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAutoAdOption $request
     * @return array|Factory|View
     */
    public function index(IndexAutoAdOption $request)
    {
        if ($request->all) {
            
            $query = AutoAdOption::query();

            $columns =  ['auto_ad_id', 'auto_option_id'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (AutoAdOption::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AutoAdOption::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['auto_ad_id', 'auto_option_id'],

            // set columns to searchIn
            ['auto_ad_id', 'auto_option_id'],
            function ($query) use ($request) {
                            
                    $columns =  ['auto_ad_id', 'auto_option_id'];

                    foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                    foreach (AutoAdOption::getRelationships() as $key => $value) {
                       $query->with($key);
                    }

                     $query->orderBy('auto_ad_id');
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
        $this->authorize('admin.auto-ad-option.create');

        return view('admin.auto-ad-option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAutoAdOption $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAutoAdOption $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AutoAdOption
        $autoAdOption = AutoAdOption::create($sanitized);

        return ['data' => $autoAdOption];
    }

    /**
     * Display the specified resource.
     *
     * @param AutoAdOption $autoAdOption
     * @throws AuthorizationException
     * @return void
     */
    public function show(AutoAdOption $autoAdOption)
    {
        $this->authorize('admin.auto-ad-option.show', $autoAdOption);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AutoAdOption $autoAdOption
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AutoAdOption $autoAdOption)
    {
        $this->authorize('admin.auto-ad-option.edit', $autoAdOption);


        return view('admin.auto-ad-option.edit', [
            'autoAdOption' => $autoAdOption,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAutoAdOption $request
     * @param AutoAdOption $autoAdOption
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAutoAdOption $request, AutoAdOption $autoAdOption)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AutoAdOption
        $autoAdOption->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/auto-ad-options'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/auto-ad-options');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAutoAdOption $request
     * @param AutoAdOption $autoAdOption
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAutoAdOption $request, AutoAdOption $autoAdOption)
    {
        $autoAdOption->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAutoAdOption $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAutoAdOption $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AutoAdOption::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
