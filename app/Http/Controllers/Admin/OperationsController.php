<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Operation\BulkDestroyOperation;
use App\Http\Requests\Admin\Operation\DestroyOperation;
use App\Http\Requests\Admin\Operation\IndexOperation;
use App\Http\Requests\Admin\Operation\StoreOperation;
use App\Http\Requests\Admin\Operation\UpdateOperation;
use App\Models\Operation;
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

class OperationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexOperation $request
     * @return array|Factory|View
     */
    public function index(IndexOperation $request)
    {
        if ($request->all) {
            
            $query = Operation::query();

            $columns =  ['id', 'name', 'context', 'status', 'status_text'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (Operation::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Operation::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'status'],

            // set columns to searchIn
            ['id', 'name', 'context', 'status', 'status_text'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'context', 'status', 'status_text'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }
                    
                foreach (Operation::getRelationships() as $key => $value) {
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
        $this->authorize('admin.operation.create');

        return view('admin.operation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreOperation $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreOperation $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Operation
        $operation = Operation::create($sanitized);

        return ['data' => $operation];
    }

    /**
     * Display the specified resource.
     *
     * @param Operation $operation
     * @throws AuthorizationException
     * @return void
     */
    public function show(Operation $operation)
    {
        $this->authorize('admin.operation.show', $operation);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Operation $operation
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Operation $operation)
    {
        $this->authorize('admin.operation.edit', $operation);


        return view('admin.operation.edit', [
            'operation' => $operation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOperation $request
     * @param Operation $operation
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateOperation $request, Operation $operation)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Operation
        $operation->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/operations'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/operations');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyOperation $request
     * @param Operation $operation
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyOperation $request, Operation $operation)
    {
        $operation->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyOperation $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyOperation $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Operation::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
