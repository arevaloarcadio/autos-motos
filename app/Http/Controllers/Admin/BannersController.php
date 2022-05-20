<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banner\BulkDestroyBanner;
use App\Http\Requests\Admin\Banner\DestroyBanner;
use App\Http\Requests\Admin\Banner\IndexBanner;
use App\Http\Requests\Admin\Banner\StoreBanner;
use App\Http\Requests\Admin\Banner\UpdateBanner;
use App\Models\Banner;
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

class BannersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexBanner $request
     * @return array|Factory|View
     */
    public function index(IndexBanner $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Banner::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'location', 'image_path', 'link', 'order_index'],

            // set columns to searchIn
            ['id', 'location', 'image_path', 'link']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.banner.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.banner.create');

        return view('admin.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBanner $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreBanner $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Banner
        $banner = Banner::create($sanitized);

        return ['data' => $banner];
    }

    /**
     * Display the specified resource.
     *
     * @param Banner $banner
     * @throws AuthorizationException
     * @return void
     */
    public function show(Banner $banner)
    {
        $this->authorize('admin.banner.show', $banner);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Banner $banner
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Banner $banner)
    {
        $this->authorize('admin.banner.edit', $banner);


        return view('admin.banner.edit', [
            'banner' => $banner,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBanner $request
     * @param Banner $banner
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateBanner $request, Banner $banner)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Banner
        $banner->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/banners'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/banners');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyBanner $request
     * @param Banner $banner
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyBanner $request, Banner $banner)
    {
        $banner->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyBanner $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyBanner $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Banner::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
