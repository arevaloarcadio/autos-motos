<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TruckAd\BulkDestroyTruckAd;
use App\Http\Requests\Admin\TruckAd\DestroyTruckAd;
use App\Http\Requests\Admin\TruckAd\IndexTruckAd;
use App\Http\Requests\Admin\TruckAd\StoreTruckAd;
use App\Http\Requests\Admin\TruckAd\UpdateTruckAd;
use App\Models\{Ad,TruckAd,DealerShowRoom};
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
                
            if ($request->filters) {
                foreach ($columns as $column) {
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
                
                
                    if ($request->filters) {
                        foreach ($columns as $column) {
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

        $ad = Ad::create([
            'slug' => Str::slug($sanitized['title']),
            'title' => $sanitized['title'],
            'description' => $sanitized['description'],
            'thumbnail' => $sanitized['thumbnail'],
            'status' => 0,
            'type' => 'moto',
            'is_featured' => 0,
            'user_id' => Auth::user()->id,
            'market_id' => $sanitized['market_id'],
            'external_id' =>null,
            'source' => null,
            'images_processing_status' => 'SUCCESSFUL',
            'images_processing_status_text' => null,
        ]);
        
        $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id']  : null;
        // Store the TruckAd
        $truckAd = TruckAd::create([
            'ad_id' =>  $ad->id,
            'make_id' => $sanitized['make_id'],
            'custom_make' => $sanitized['custom_make'],
            'model' => $sanitized['model'],
            'truck_type' => $sanitized['truck_type'],
            'fuel_type_id' => $sanitized['fuel_type_id'],
            'vehicle_category_id' => $sanitized['vehicle_category_id'],
            'transmission_type_id' => $sanitized['transmission_type_id'],
            'cab' => $sanitized['cab'],
            'construction_year' => $sanitized['construction_year'],
            'first_registration_month' => $sanitized['first_registration_month'],
            'first_registration_year' => $sanitized['first_registration_year'],
            'inspection_valid_until_month' => $sanitized['inspection_valid_until_month'],
            'inspection_valid_until_year' => $sanitized['inspection_valid_until_year'],
            'owners' => $sanitized['owners'],
            'construction_height_mm' => $sanitized['construction_height_mm'],
            'lifting_height_mm' => $sanitized['lifting_height_mm'],
            'lifting_capacity_kg_m' => $sanitized['lifting_capacity_kg_m'],
            'permanent_total_weight_kg' => $sanitized['permanent_total_weight_kg'],
            'allowed_pulling_weight_kg' => $sanitized['allowed_pulling_weight_kg'],
            'payload_kg' => $sanitized['payload_kg'],
            'max_weight_allowed_kg' => $sanitized['max_weight_allowed_kg'],
            'empty_weight_kg' => $sanitized['empty_weight_kg'],
            'loading_space_length_mm' =>$sanitized['loading_space_length_mm'],
            'loading_space_width_mm' => $sanitized['loading_space_width_mm'],
            'loading_space_height_mm' =>  $sanitized['loading_space_height_mm'],
            'loading_volume_m3' => $sanitized['loading_volume_m3'],
            'load_capacity_kg' =>$sanitized['load_capacity_kg'],
            'operating_weight_kg' => $sanitized['operating_weight_kg'],
            'operating_hours' => $sanitized['operating_hours'],
            'axes' => $sanitized['axes'],
            'wheel_formula' => $sanitized['wheel_formula'],
            'hydraulic_system' => $sanitized['hydraulic_system'],
            'seats' => $sanitized['seats'],
            'mileage' => $sanitized['mileage'],
            'power_kw' => $sanitized['power_kw'],
            'emission_class' => $sanitized['emission_class'],
            'fuel_consumption' => $sanitized['fuel_consumption'],
            'co2_emissions' => $sanitized['co2_emissions'],
            'condition' => $sanitized['condition'],
            'interior_color' => $sanitized['interior_color'],
            'exterior_color' => $sanitized['exterior_color'],
            'price' => $sanitized['price'],
            'price_contains_vat' => $sanitized['price_contains_vat'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' => $sanitized['first_name'],
            'last_name' => $sanitized['last_name'],
            'email_address' => $sanitized['email_address'],
            'address' => $sanitized['address'],
            'zip_code' => $sanitized['zip_code'],
            'city' => $sanitized['city'],
            'country' => $sanitized['country'],
            'mobile_number' => $sanitized['mobile_number'],
            'landline_number' => $sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' => $sanitized['youtube_link'],
            
        ]);

        return ['data' => ['ad' => $ad,'truck_ad' => $truckAd]];
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
