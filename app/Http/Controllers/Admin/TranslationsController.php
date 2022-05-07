<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Translation\BulkDestroyTranslation;
use App\Http\Requests\Admin\Translation\DestroyTranslation;
use App\Http\Requests\Admin\Translation\IndexTranslation;
use App\Http\Requests\Admin\Translation\StoreTranslation;
use App\Http\Requests\Admin\Translation\UpdateTranslation;
use App\Models\Translation;
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

class TranslationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTranslation $request
     * @return array|Factory|View
     */
    public function index(IndexTranslation $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Translation::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'locale_id', 'translation_key', 'resource_id'],

            // set columns to searchIn
            ['id', 'locale_id', 'translation_key', 'translation_value', 'resource_id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.translation.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.translation.create');

        return view('admin.translation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTranslation $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTranslation $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Translation
        $translation = Translation::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/translations'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/translations');
    }

    /**
     * Display the specified resource.
     *
     * @param Translation $translation
     * @throws AuthorizationException
     * @return void
     */
    public function show(Translation $translation)
    {
        $this->authorize('admin.translation.show', $translation);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Translation $translation
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Translation $translation)
    {
        $this->authorize('admin.translation.edit', $translation);


        return view('admin.translation.edit', [
            'translation' => $translation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTranslation $request
     * @param Translation $translation
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTranslation $request, Translation $translation)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Translation
        $translation->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/translations'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/translations');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTranslation $request
     * @param Translation $translation
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTranslation $request, Translation $translation)
    {
        $translation->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTranslation $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTranslation $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Translation::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
