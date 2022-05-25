<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MobileHomeAdOption\BulkDestroyMobileHomeAdOption;
use App\Http\Requests\Admin\MobileHomeAdOption\DestroyMobileHomeAdOption;
use App\Http\Requests\Admin\MobileHomeAdOption\IndexMobileHomeAdOption;
use App\Http\Requests\Admin\MobileHomeAdOption\StoreMobileHomeAdOption;
use App\Http\Requests\Admin\MobileHomeAdOption\UpdateMobileHomeAdOption;
use App\Models\MobileHomeAdOption;
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

class MobileHomeAdOptionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMobileHomeAdOption $request
     * @return array|Factory|View
     */
    public function index(IndexMobileHomeAdOption $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MobileHomeAdOption::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['mobile_home_ad_id', 'option_id'],

            // set columns to searchIn
            ['mobile_home_ad_id', 'option_id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.mobile-home-ad-option.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.mobile-home-ad-option.create');

        return view('admin.mobile-home-ad-option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMobileHomeAdOption $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMobileHomeAdOption $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the MobileHomeAdOption
        $mobileHomeAdOption = MobileHomeAdOption::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/mobile-home-ad-options'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/mobile-home-ad-options');
    }

    /**
     * Display the specified resource.
     *
     * @param MobileHomeAdOption $mobileHomeAdOption
     * @throws AuthorizationException
     * @return void
     */
    public function show(MobileHomeAdOption $mobileHomeAdOption)
    {
        $this->authorize('admin.mobile-home-ad-option.show', $mobileHomeAdOption);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MobileHomeAdOption $mobileHomeAdOption
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MobileHomeAdOption $mobileHomeAdOption)
    {
        $this->authorize('admin.mobile-home-ad-option.edit', $mobileHomeAdOption);


        return view('admin.mobile-home-ad-option.edit', [
            'mobileHomeAdOption' => $mobileHomeAdOption,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMobileHomeAdOption $request
     * @param MobileHomeAdOption $mobileHomeAdOption
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMobileHomeAdOption $request, MobileHomeAdOption $mobileHomeAdOption)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MobileHomeAdOption
        $mobileHomeAdOption->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/mobile-home-ad-options'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/mobile-home-ad-options');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMobileHomeAdOption $request
     * @param MobileHomeAdOption $mobileHomeAdOption
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMobileHomeAdOption $request, MobileHomeAdOption $mobileHomeAdOption)
    {
        $mobileHomeAdOption->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMobileHomeAdOption $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMobileHomeAdOption $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MobileHomeAdOption::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
