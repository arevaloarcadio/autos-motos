<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AutoOption\BulkDestroyAutoOption;
use App\Http\Requests\Admin\AutoOption\DestroyAutoOption;
use App\Http\Requests\Admin\AutoOption\IndexAutoOption;
use App\Http\Requests\Admin\AutoOption\StoreAutoOption;
use App\Http\Requests\Admin\AutoOption\UpdateAutoOption;
use App\Models\AutoOption;
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

class AutoOptionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAutoOption $request
     * @return array|Factory|View
     */
    public function index(IndexAutoOption $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AutoOption::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'internal_name', 'parent_id', 'ad_type'],

            // set columns to searchIn
            ['id', 'internal_name', 'slug', 'parent_id', 'ad_type']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.auto-option.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.auto-option.create');

        return view('admin.auto-option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAutoOption $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAutoOption $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AutoOption
        $autoOption = AutoOption::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/auto-options'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/auto-options');
    }

    /**
     * Display the specified resource.
     *
     * @param AutoOption $autoOption
     * @throws AuthorizationException
     * @return void
     */
    public function show(AutoOption $autoOption)
    {
        $this->authorize('admin.auto-option.show', $autoOption);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AutoOption $autoOption
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AutoOption $autoOption)
    {
        $this->authorize('admin.auto-option.edit', $autoOption);


        return view('admin.auto-option.edit', [
            'autoOption' => $autoOption,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAutoOption $request
     * @param AutoOption $autoOption
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAutoOption $request, AutoOption $autoOption)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AutoOption
        $autoOption->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/auto-options'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/auto-options');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAutoOption $request
     * @param AutoOption $autoOption
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAutoOption $request, AutoOption $autoOption)
    {
        $autoOption->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAutoOption $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAutoOption $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AutoOption::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
