<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Plan\BulkDestroyPlan;
use App\Http\Requests\Admin\Plan\DestroyPlan;
use App\Http\Requests\Admin\Plan\IndexPlan;
use App\Http\Requests\Admin\Plan\StorePlan;
use App\Http\Requests\Admin\Plan\UpdatePlan;
use App\Models\Plan;
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

class PlansController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexPlan $request
     * @return array|Factory|View
     */
    public function index(IndexPlan $request)
    {
        if ($request->all) {
            
            $query = Plan::query();

            $columns =   ['id', 'name', 'price'];
           
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            /*foreach (Plan::getRelationships() as $key => $value) {
               $query->with($key);
            }*/

            return ['data' => $query->get()];
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Plan::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'price'],

            // set columns to searchIn
            ['id', 'name', 'price']
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
        $this->authorize('admin.plan.create');

        return view('admin.plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePlan $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StorePlan $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Plan
        $plan = Plan::create($sanitized);

        return  ['data'=> $plan];
    }

    /**
     * Display the specified resource.
     *
     * @param Plan $plan
     * @throws AuthorizationException
     * @return void
     */
    public function show(Plan $plan)
    {
        $this->authorize('admin.plan.show', $plan);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Plan $plan
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Plan $plan)
    {
        $this->authorize('admin.plan.edit', $plan);


        return view('admin.plan.edit', [
            'plan' => $plan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePlan $request
     * @param Plan $plan
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePlan $request, Plan $plan)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Plan
        $plan->update($sanitized);

        return  ['data'=> $plan];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPlan $request
     * @param Plan $plan
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyPlan $request, Plan $plan)
    {
        $plan->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPlan $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPlan $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Plan::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
