<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotedAd\BulkDestroyPromotedAd;
use App\Http\Requests\Admin\PromotedAd\DestroyPromotedAd;
use App\Http\Requests\Admin\PromotedAd\IndexPromotedAd;
use App\Http\Requests\Admin\PromotedAd\StorePromotedAd;
use App\Http\Requests\Admin\PromotedAd\UpdatePromotedAd;
use App\Models\{PromotedAd,User,Plan,Ad};
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
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PromotedAdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexPromotedAd $request
     * @return array|Factory|View
     */
    public function index(IndexPromotedAd $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(PromotedAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'user_id'],

            // set columns to searchIn
            ['id', 'ad_id', 'user_id']
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
        $this->authorize('admin.promoted-ad.create');

        return view('admin.promoted-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePromotedAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StorePromotedAd $request)
    {
        $resource = ApiHelper::resource();

        try {
            return response()->json(['data' => 'EN DESARROLLO'], 200);
            // Sanitize input
            $data = [];
            $sanitized = $request->getSanitized();
            $data['user_id'] = Auth::user()->id;
            $data['ad_id'] =  $sanitized['ad_id'];

            $user = Auth::user();
            
            if ($user->plan_active === null) {
                ApiHelper::setError($resource, 0, 422, ['data' => 'Actualemente no tiene plan activo para promocionar']);
                return $this->sendResponse($resource);
            }
            
            $ad = Ad::find($data['ad_id']);

            $plan = Plan::find($user->plan_active->id);

            if ($user->type == 'Ocasional') {
                 $plan->characteristic_promotion_plans; 
            }

            $promotedAd = PromotedAd::create($data);

            return response()->json(['data' => $promotedAd], 200);
        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param PromotedAd $promotedAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(PromotedAd $promotedAd)
    {
        $this->authorize('admin.promoted-ad.show', $promotedAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PromotedAd $promotedAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(PromotedAd $promotedAd)
    {
        $this->authorize('admin.promoted-ad.edit', $promotedAd);


        return view('admin.promoted-ad.edit', [
            'promotedAd' => $promotedAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePromotedAd $request
     * @param PromotedAd $promotedAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePromotedAd $request, PromotedAd $promotedAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values PromotedAd
        $promotedAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/promoted-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/promoted-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPromotedAd $request
     * @param PromotedAd $promotedAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyPromotedAd $request, PromotedAd $promotedAd)
    {
        $promotedAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPromotedAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPromotedAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    PromotedAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
