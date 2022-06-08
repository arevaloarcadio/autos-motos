<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdImageVersion\BulkDestroyAdImageVersion;
use App\Http\Requests\Admin\AdImageVersion\DestroyAdImageVersion;
use App\Http\Requests\Admin\AdImageVersion\IndexAdImageVersion;
use App\Http\Requests\Admin\AdImageVersion\StoreAdImageVersion;
use App\Http\Requests\Admin\AdImageVersion\UpdateAdImageVersion;
use App\Models\AdImageVersion;
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

class AdImageVersionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdImageVersion $request
     * @return array|Factory|View
     */
    public function index(IndexAdImageVersion $request)
    {
        if ($request->all) {
            
            $query = AdImageVersion::query();

            $columns =  ['id', 'ad_image_id', 'name', 'path', 'is_external'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (AdImageVersion::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdImageVersion::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_image_id', 'name', 'path', 'is_external'],

            // set columns to searchIn
            ['id', 'ad_image_id', 'name', 'path'],

        function ($query) use ($request) {
                        
                $columns = ['id', 'ad_image_id', 'name', 'path', 'is_external'];
                
                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (AdImageVersion::getRelationships() as $key => $value) {
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
        $this->authorize('admin.ad-image-version.create');

        return view('admin.ad-image-version.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdImageVersion $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdImageVersion $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AdImageVersion
        $adImageVersion = AdImageVersion::create($sanitized);

        return ['data' => $adImageVersion];
    }

    /**
     * Display the specified resource.
     *
     * @param AdImageVersion $adImageVersion
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdImageVersion $adImageVersion)
    {
        $this->authorize('admin.ad-image-version.show', $adImageVersion);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdImageVersion $adImageVersion
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdImageVersion $adImageVersion)
    {
        $this->authorize('admin.ad-image-version.edit', $adImageVersion);


        return view('admin.ad-image-version.edit', [
            'adImageVersion' => $adImageVersion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdImageVersion $request
     * @param AdImageVersion $adImageVersion
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdImageVersion $request, AdImageVersion $adImageVersion)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AdImageVersion
        $adImageVersion->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ad-image-versions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ad-image-versions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdImageVersion $request
     * @param AdImageVersion $adImageVersion
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdImageVersion $request, AdImageVersion $adImageVersion)
    {
        $adImageVersion->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAdImageVersion $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAdImageVersion $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AdImageVersion::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
