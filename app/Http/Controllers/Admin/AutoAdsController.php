<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AutoAd\BulkDestroyAutoAd;
use App\Http\Requests\Admin\AutoAd\DestroyAutoAd;
use App\Http\Requests\Admin\AutoAd\IndexAutoAd;
use App\Http\Requests\Admin\AutoAd\StoreAutoAd;
use App\Http\Requests\Admin\AutoAd\UpdateAutoAd;
use App\Models\AutoAd;
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

class AutoAdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAutoAd $request
     * @return array|Factory|View
     */
    public function index(IndexAutoAd $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AutoAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'price', 'price_contains_vat', 'vin', 'doors', 'mileage', 'exterior_color', 'interior_color', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link', 'ad_fuel_type_id', 'ad_body_type_id', 'ad_transmission_type_id', 'ad_drive_type_id', 'first_registration_month', 'first_registration_year', 'engine_displacement', 'power_hp', 'owners', 'inspection_valid_until_month', 'inspection_valid_until_year', 'make_id', 'model_id', 'generation_id', 'series_id', 'trim_id', 'equipment_id', 'additional_vehicle_info', 'seats', 'fuel_consumption', 'co2_emissions', 'latitude', 'longitude', 'geocoding_status'],

            // set columns to searchIn
            ['id', 'ad_id', 'vin', 'exterior_color', 'interior_color', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link', 'make_id', 'model_id', 'equipment_id', 'additional_vehicle_info', 'latitude', 'longitude', 'geocoding_status'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'ad_id', 'price', 'price_contains_vat', 'vin', 'doors', 'mileage', 'exterior_color', 'interior_color', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link', 'ad_fuel_type_id', 'ad_body_type_id', 'ad_transmission_type_id', 'ad_drive_type_id', 'first_registration_month', 'first_registration_year', 'engine_displacement', 'power_hp', 'owners', 'inspection_valid_until_month', 'inspection_valid_until_year', 'make_id', 'model_id', 'generation_id', 'series_id', 'trim_id', 'equipment_id', 'additional_vehicle_info', 'seats', 'fuel_consumption', 'co2_emissions', 'latitude', 'longitude', 'geocoding_status'];
                
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }

                foreach (AutoAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.auto-ad.create');

        return view('admin.auto-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAutoAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAutoAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AutoAd
        $autoAd = AutoAd::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/auto-ads'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/auto-ads');
    }

    /**
     * Display the specified resource.
     *
     * @param AutoAd $autoAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(AutoAd $autoAd)
    {
        $this->authorize('admin.auto-ad.show', $autoAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AutoAd $autoAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AutoAd $autoAd)
    {
        $this->authorize('admin.auto-ad.edit', $autoAd);


        return view('admin.auto-ad.edit', [
            'autoAd' => $autoAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAutoAd $request
     * @param AutoAd $autoAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAutoAd $request, AutoAd $autoAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AutoAd
        $autoAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/auto-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/auto-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAutoAd $request
     * @param AutoAd $autoAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAutoAd $request, AutoAd $autoAd)
    {
        $autoAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAutoAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAutoAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AutoAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
