<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdMake\BulkDestroyAdMake;
use App\Http\Requests\Admin\AdMake\DestroyAdMake;
use App\Http\Requests\Admin\AdMake\IndexAdMake;
use App\Http\Requests\Admin\AdMake\StoreAdMake;
use App\Http\Requests\Admin\AdMake\UpdateAdMake;
use App\Models\AdMake;
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

class AdMakesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdMake $request
     * @return array|Factory|View
     */
    public function index(IndexAdMake $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdMake::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'ad_type'],

            // set columns to searchIn
            ['id', 'name', 'slug', 'ad_type'],

        function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'slug', 'ad_type'];

                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (AdMake::getRelationships() as $key => $value) {
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
        $this->authorize('admin.ad-make.create');

        return view('admin.ad-make.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdMake $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdMake $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AdMake
        $adMake = AdMake::create($sanitized);

        return ['data' => $adMake];
    }

    /**
     * Display the specified resource.
     *
     * @param AdMake $adMake
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdMake $adMake)
    {
        $this->authorize('admin.ad-make.show', $adMake);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdMake $adMake
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdMake $adMake)
    {
        $this->authorize('admin.ad-make.edit', $adMake);


        return view('admin.ad-make.edit', [
            'adMake' => $adMake,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdMake $request
     * @param AdMake $adMake
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdMake $request, AdMake $adMake)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AdMake
        $adMake->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ad-makes'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ad-makes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdMake $request
     * @param AdMake $adMake
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdMake $request, AdMake $adMake)
    {
        $adMake->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAdMake $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAdMake $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AdMake::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
