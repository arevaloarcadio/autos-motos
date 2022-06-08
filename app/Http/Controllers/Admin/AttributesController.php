<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Attribute\BulkDestroyAttribute;
use App\Http\Requests\Admin\Attribute\DestroyAttribute;
use App\Http\Requests\Admin\Attribute\IndexAttribute;
use App\Http\Requests\Admin\Attribute\StoreAttribute;
use App\Http\Requests\Admin\Attribute\UpdateAttribute;
use App\Models\Attribute;
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

class AttributesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAttribute $request
     * @return array|Factory|View
     */
    public function index(IndexAttribute $request)
    {
        if ($request->all) {
            
            $query = Table::query();

            $columns =  ['id', 'name', 'searched', 'featured', 'is_choice', 'order_level'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (Table::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Attribute::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'searched', 'featured', 'is_choice', 'order_level'],

            // set columns to searchIn
            ['id', 'name']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.attribute.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.attribute.create');

        return view('admin.attribute.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAttribute $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAttribute $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Attribute
        $attribute = Attribute::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/attributes'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/attributes');
    }

    /**
     * Display the specified resource.
     *
     * @param Attribute $attribute
     * @throws AuthorizationException
     * @return void
     */
    public function show(Attribute $attribute)
    {
        $this->authorize('admin.attribute.show', $attribute);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Attribute $attribute
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Attribute $attribute)
    {
        $this->authorize('admin.attribute.edit', $attribute);


        return view('admin.attribute.edit', [
            'attribute' => $attribute,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAttribute $request
     * @param Attribute $attribute
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAttribute $request, Attribute $attribute)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Attribute
        $attribute->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/attributes'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/attributes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAttribute $request
     * @param Attribute $attribute
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAttribute $request, Attribute $attribute)
    {
        $attribute->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAttribute $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAttribute $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Attribute::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
