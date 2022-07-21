<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdUserPlan\BulkDestroyAdUserPlan;
use App\Http\Requests\Admin\AdUserPlan\DestroyAdUserPlan;
use App\Http\Requests\Admin\AdUserPlan\IndexAdUserPlan;
use App\Http\Requests\Admin\AdUserPlan\StoreAdUserPlan;
use App\Http\Requests\Admin\AdUserPlan\UpdateAdUserPlan;
use App\Models\AdUserPlan;
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

class AdUserPlansController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdUserPlan $request
     * @return array|Factory|View
     */
    public function index(IndexAdUserPlan $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdUserPlan::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'plan_user_id', 'ad_id'],

            // set columns to searchIn
            ['id', 'plan_user_id', 'ad_id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.ad-user-plan.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.ad-user-plan.create');

        return view('admin.ad-user-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdUserPlan $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdUserPlan $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AdUserPlan
        $adUserPlan = AdUserPlan::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/ad-user-plans'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/ad-user-plans');
    }

    /**
     * Display the specified resource.
     *
     * @param AdUserPlan $adUserPlan
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdUserPlan $adUserPlan)
    {
        $this->authorize('admin.ad-user-plan.show', $adUserPlan);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdUserPlan $adUserPlan
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdUserPlan $adUserPlan)
    {
        $this->authorize('admin.ad-user-plan.edit', $adUserPlan);


        return view('admin.ad-user-plan.edit', [
            'adUserPlan' => $adUserPlan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdUserPlan $request
     * @param AdUserPlan $adUserPlan
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdUserPlan $request, AdUserPlan $adUserPlan)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AdUserPlan
        $adUserPlan->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ad-user-plans'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ad-user-plans');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdUserPlan $request
     * @param AdUserPlan $adUserPlan
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdUserPlan $request, AdUserPlan $adUserPlan)
    {
        $adUserPlan->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAdUserPlan $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAdUserPlan $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AdUserPlan::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
