<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CharacteristicPlan\BulkDestroyCharacteristicPlan;
use App\Http\Requests\Admin\CharacteristicPlan\DestroyCharacteristicPlan;
use App\Http\Requests\Admin\CharacteristicPlan\IndexCharacteristicPlan;
use App\Http\Requests\Admin\CharacteristicPlan\StoreCharacteristicPlan;
use App\Http\Requests\Admin\CharacteristicPlan\UpdateCharacteristicPlan;
use App\Models\CharacteristicPlan;
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

class CharacteristicPlansController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCharacteristicPlan $request
     * @return array|Factory|View
     */
    public function index(IndexCharacteristicPlan $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CharacteristicPlan::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'vehicle_ads', 'rental_ads', 'promotion_month', 'front_page_promotion', 'video_a_day', 'mechanics_rental_ads', 'plan_id'],

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

        return view('admin.characteristic-plan.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.characteristic-plan.create');

        return view('admin.characteristic-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCharacteristicPlan $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCharacteristicPlan $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CharacteristicPlan
        $characteristicPlan = CharacteristicPlan::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/characteristic-plans'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/characteristic-plans');
    }

    /**
     * Display the specified resource.
     *
     * @param CharacteristicPlan $characteristicPlan
     * @throws AuthorizationException
     * @return void
     */
    public function show(CharacteristicPlan $characteristicPlan)
    {
        $this->authorize('admin.characteristic-plan.show', $characteristicPlan);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CharacteristicPlan $characteristicPlan
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CharacteristicPlan $characteristicPlan)
    {
        $this->authorize('admin.characteristic-plan.edit', $characteristicPlan);


        return view('admin.characteristic-plan.edit', [
            'characteristicPlan' => $characteristicPlan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCharacteristicPlan $request
     * @param CharacteristicPlan $characteristicPlan
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCharacteristicPlan $request, CharacteristicPlan $characteristicPlan)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CharacteristicPlan
        $characteristicPlan->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/characteristic-plans'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/characteristic-plans');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCharacteristicPlan $request
     * @param CharacteristicPlan $characteristicPlan
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCharacteristicPlan $request, CharacteristicPlan $characteristicPlan)
    {
        $characteristicPlan->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCharacteristicPlan $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCharacteristicPlan $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    CharacteristicPlan::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
