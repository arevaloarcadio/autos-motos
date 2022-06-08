<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TruckAd\BulkDestroyTruckAd;
use App\Http\Requests\Admin\TruckAd\DestroyTruckAd;
use App\Http\Requests\Admin\TruckAd\IndexTruckAd;
use App\Http\Requests\Admin\TruckAd\StoreTruckAd;
use App\Http\Requests\Admin\TruckAd\UpdateTruckAd;
use App\Models\TruckAd;
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

class TruckAdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTruckAd $request
     * @return array|Factory|View
     */
    public function index(IndexTruckAd $request)
    {
        if ($request->all) {
            
            $query = TruckAd::query();

            $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'construction_height_mm', 'lifting_height_mm', 'lifting_capacity_kg_m', 'permanent_total_weight_kg', 'allowed_pulling_weight_kg', 'payload_kg', 'max_weight_allowed_kg', 'empty_weight_kg', 'loading_space_length_mm', 'loading_space_width_mm', 'loading_space_height_mm', 'loading_volume_m3', 'load_capacity_kg', 'operating_weight_kg', 'operating_hours', 'axes', 'wheel_formula', 'hydraulic_system', 'seats', 'mileage', 'power_kw', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'interior_color', 'exterior_color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (TruckAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TruckAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'construction_height_mm', 'lifting_height_mm', 'lifting_capacity_kg_m', 'permanent_total_weight_kg', 'allowed_pulling_weight_kg', 'payload_kg', 'max_weight_allowed_kg', 'empty_weight_kg', 'loading_space_length_mm', 'loading_space_width_mm', 'loading_space_height_mm', 'loading_volume_m3', 'load_capacity_kg', 'operating_weight_kg', 'operating_hours', 'axes', 'wheel_formula', 'hydraulic_system', 'seats', 'mileage', 'power_kw', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'interior_color', 'exterior_color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            // set columns to searchIn
            ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'wheel_formula', 'hydraulic_system', 'emission_class', 'condition', 'interior_color', 'exterior_color', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model', 'truck_type', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'cab', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'construction_height_mm', 'lifting_height_mm', 'lifting_capacity_kg_m', 'permanent_total_weight_kg', 'allowed_pulling_weight_kg', 'payload_kg', 'max_weight_allowed_kg', 'empty_weight_kg', 'loading_space_length_mm', 'loading_space_width_mm', 'loading_space_height_mm', 'loading_volume_m3', 'load_capacity_kg', 'operating_weight_kg', 'operating_hours', 'axes', 'wheel_formula', 'hydraulic_system', 'seats', 'mileage', 'power_kw', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'interior_color', 'exterior_color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (TruckAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.truck-ad.create');

        return view('admin.truck-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTruckAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTruckAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the TruckAd
        $truckAd = TruckAd::create($sanitized);

        return ['data' => $truckAd];
    }

    /**
     * Display the specified resource.
     *
     * @param TruckAd $truckAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(TruckAd $truckAd)
    {
        $this->authorize('admin.truck-ad.show', $truckAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TruckAd $truckAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TruckAd $truckAd)
    {
        $this->authorize('admin.truck-ad.edit', $truckAd);


        return view('admin.truck-ad.edit', [
            'truckAd' => $truckAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTruckAd $request
     * @param TruckAd $truckAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTruckAd $request, TruckAd $truckAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values TruckAd
        $truckAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/truck-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/truck-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTruckAd $request
     * @param TruckAd $truckAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTruckAd $request, TruckAd $truckAd)
    {
        $truckAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTruckAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTruckAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    TruckAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
