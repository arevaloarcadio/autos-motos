<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttributeValue\BulkDestroyAttributeValue;
use App\Http\Requests\Admin\AttributeValue\DestroyAttributeValue;
use App\Http\Requests\Admin\AttributeValue\IndexAttributeValue;
use App\Http\Requests\Admin\AttributeValue\StoreAttributeValue;
use App\Http\Requests\Admin\AttributeValue\UpdateAttributeValue;
use App\Models\AttributeValue;
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

class AttributeValuesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAttributeValue $request
     * @return array|Factory|View
     */
    public function index(IndexAttributeValue $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AttributeValue::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'attribute_id', 'value', 'color_code', 'ads_type'],

            // set columns to searchIn
            ['id', 'value', 'color_code']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.attribute-value.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.attribute-value.create');

        return view('admin.attribute-value.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAttributeValue $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAttributeValue $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AttributeValue
        $attributeValue = AttributeValue::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/attribute-values'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/attribute-values');
    }

    /**
     * Display the specified resource.
     *
     * @param AttributeValue $attributeValue
     * @throws AuthorizationException
     * @return void
     */
    public function show(AttributeValue $attributeValue)
    {
        $this->authorize('admin.attribute-value.show', $attributeValue);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AttributeValue $attributeValue
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AttributeValue $attributeValue)
    {
        $this->authorize('admin.attribute-value.edit', $attributeValue);


        return view('admin.attribute-value.edit', [
            'attributeValue' => $attributeValue,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAttributeValue $request
     * @param AttributeValue $attributeValue
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAttributeValue $request, AttributeValue $attributeValue)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AttributeValue
        $attributeValue->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/attribute-values'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/attribute-values');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAttributeValue $request
     * @param AttributeValue $attributeValue
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAttributeValue $request, AttributeValue $attributeValue)
    {
        $attributeValue->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAttributeValue $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAttributeValue $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AttributeValue::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
