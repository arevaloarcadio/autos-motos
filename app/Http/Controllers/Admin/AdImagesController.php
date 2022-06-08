<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdImage\BulkDestroyAdImage;
use App\Http\Requests\Admin\AdImage\DestroyAdImage;
use App\Http\Requests\Admin\AdImage\IndexAdImage;
use App\Http\Requests\Admin\AdImage\StoreAdImage;
use App\Http\Requests\Admin\AdImage\UpdateAdImage;
use App\Models\AdImage;
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

class AdImagesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdImage $request
     * @return array|Factory|View
     */
    public function index(IndexAdImage $request)
    {
       if ($request->all) {
            
            $query = AdImage::query();

            $columns = 
            ['id', 'ad_id', 'path', 'is_external', 'order_index'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (AdImage::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdImage::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'path', 'is_external', 'order_index'],

            // set columns to searchIn
            ['id', 'ad_id', 'path'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'ad_id', 'path', 'is_external', 'order_index'];
                
                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (AdImage::getRelationships() as $key => $value) {
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
        $this->authorize('admin.ad-image.create');

        return view('admin.ad-image.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdImage $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdImage $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AdImage
        $adImage = AdImage::create($sanitized);

        return ['data' => $adImage];
    }

    /**
     * Display the specified resource.
     *
     * @param AdImage $adImage
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdImage $adImage)
    {
        $this->authorize('admin.ad-image.show', $adImage);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdImage $adImage
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdImage $adImage)
    {
        $this->authorize('admin.ad-image.edit', $adImage);


        return view('admin.ad-image.edit', [
            'adImage' => $adImage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdImage $request
     * @param AdImage $adImage
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdImage $request, AdImage $adImage)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AdImage
        $adImage->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ad-images'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ad-images');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdImage $request
     * @param AdImage $adImage
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdImage $request, AdImage $adImage)
    {
        $adImage->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAdImage $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAdImage $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AdImage::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
