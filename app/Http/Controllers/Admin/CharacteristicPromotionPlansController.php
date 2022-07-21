<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CharacteristicPromotionPlan\BulkDestroyCharacteristicPromotionPlan;
use App\Http\Requests\Admin\CharacteristicPromotionPlan\DestroyCharacteristicPromotionPlan;
use App\Http\Requests\Admin\CharacteristicPromotionPlan\IndexCharacteristicPromotionPlan;
use App\Http\Requests\Admin\CharacteristicPromotionPlan\StoreCharacteristicPromotionPlan;
use App\Http\Requests\Admin\CharacteristicPromotionPlan\UpdateCharacteristicPromotionPlan;
use App\Models\CharacteristicPromotionPlan;
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

class CharacteristicPromotionPlansController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCharacteristicPromotionPlan $request
     * @return array|Factory|View
     */
    public function index(IndexCharacteristicPromotionPlan $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CharacteristicPromotionPlan::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'vehicle_ads', 'shop_ads', 'rental_ads', 'mechanic_ads', 'front_page_promotion', 'plan_id'],

            // set columns to searchIn
            ['id', 'plan_id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.characteristic-promotion-plan.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.characteristic-promotion-plan.create');

        return view('admin.characteristic-promotion-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCharacteristicPromotionPlan $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCharacteristicPromotionPlan $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CharacteristicPromotionPlan
        $characteristicPromotionPlan = CharacteristicPromotionPlan::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/characteristic-promotion-plans'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/characteristic-promotion-plans');
    }

    /**
     * Display the specified resource.
     *
     * @param CharacteristicPromotionPlan $characteristicPromotionPlan
     * @throws AuthorizationException
     * @return void
     */
    public function show(CharacteristicPromotionPlan $characteristicPromotionPlan)
    {
        $this->authorize('admin.characteristic-promotion-plan.show', $characteristicPromotionPlan);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CharacteristicPromotionPlan $characteristicPromotionPlan
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CharacteristicPromotionPlan $characteristicPromotionPlan)
    {
        $this->authorize('admin.characteristic-promotion-plan.edit', $characteristicPromotionPlan);


        return view('admin.characteristic-promotion-plan.edit', [
            'characteristicPromotionPlan' => $characteristicPromotionPlan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCharacteristicPromotionPlan $request
     * @param CharacteristicPromotionPlan $characteristicPromotionPlan
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCharacteristicPromotionPlan $request, CharacteristicPromotionPlan $characteristicPromotionPlan)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CharacteristicPromotionPlan
        $characteristicPromotionPlan->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/characteristic-promotion-plans'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/characteristic-promotion-plans');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCharacteristicPromotionPlan $request
     * @param CharacteristicPromotionPlan $characteristicPromotionPlan
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCharacteristicPromotionPlan $request, CharacteristicPromotionPlan $characteristicPromotionPlan)
    {
        $characteristicPromotionPlan->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCharacteristicPromotionPlan $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCharacteristicPromotionPlan $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CharacteristicPromotionPlan::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
