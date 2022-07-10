<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AutoAd\BulkDestroyAutoAd;
use App\Http\Requests\Admin\AutoAd\DestroyAutoAd;
use App\Http\Requests\Admin\AutoAd\IndexAutoAd;
use App\Http\Requests\Admin\AutoAd\StoreAutoAd;
use App\Http\Requests\Admin\AutoAd\UpdateAutoAd;
use App\Models\{Ad,AutoAd,DealerShowRoom};
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        if ($request->all) {
            
            $query = AutoAd::query();

            $columns = ['id', 'ad_id', 'price', 'price_contains_vat', 'vin', 'doors', 'mileage', 'exterior_color', 'interior_color', 'condition', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link', 'ad_fuel_type_id', 'ad_body_type_id', 'ad_transmission_type_id', 'ad_drive_type_id', 'first_registration_month', 'first_registration_year', 'engine_displacement', 'power_hp', 'owners', 'inspection_valid_until_month', 'inspection_valid_until_year', 'make_id', 'model_id', 'generation_id', 'series_id', 'trim_id', 'equipment_id', 'additional_vehicle_info', 'seats', 'fuel_consumption', 'co2_emissions', 'latitude', 'longitude', 'geocoding_status'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (AutoAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
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
                    foreach ($columns as $column) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
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

        $ad = Ad::create([
            'slug' => Str::slug($sanitized['title']),
            'title' => $sanitized['title'],
            'description' => $sanitized['description'],
            'thumbnail' => $sanitized['thumbnail'],
            'status' => 0,
            'type' => 'auto',
            'is_featured' => 0,
            'user_id' => Auth::user()->id,
            'market_id' => $sanitized['market_id'],
            'external_id' =>null,
            'source' => null,
            'images_processing_status' => 'SUCCESSFUL',
            'images_processing_status_text' => null,
        ]);
        
        $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;

        $autoAd = AutoAd::create([
            
            'ad_id' =>  $ad->id,
            'price' => $sanitized['price'],
            'price_contains_vat' => 0,
            'vin' => null,
            'doors' => $sanitized['doors'],
            'mileage' => $sanitized['mileage'],
            'exterior_color' => $sanitized['exterior_color'],
            'interior_color' =>$sanitized['interior_color'],
            'condition' => $sanitized['condition'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' =>  $sanitized['first_name'],
            'last_name' =>  $sanitized['last_name'],
            'email_address' =>  $sanitized['email_address'],
            'address' =>  $sanitized['address'],
            'zip_code' =>  $sanitized['zip_code'],
            'city' =>  $sanitized['city'],
            'country' =>  $sanitized['country'],
            'mobile_number' =>  $sanitized['mobile_number'],
            'landline_number' =>  $sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' => $sanitized['youtube_link'],
            'ad_fuel_type_id' =>  $sanitized['ad_fuel_type_id'],
            'ad_body_type_id' =>  $sanitized['ad_body_type_id'],
            'ad_transmission_type_id' =>  $sanitized['ad_transmission_type_id'],
            'ad_drive_type_id' =>  $sanitized['ad_drive_type_id'],
            'first_registration_month' =>  $sanitized['first_registration_month'],
            'first_registration_year' =>  $sanitized['first_registration_year'],
            'engine_displacement' =>  $sanitized['engine_displacement'],
            'power_hp' => $sanitized['power_hp'],
            'owners' =>  $sanitized['owners'],
            'inspection_valid_until_month' =>  $sanitized['inspection_valid_until_month'],
            'inspection_valid_until_year' => $sanitized['inspection_valid_until_year'],
            'make_id' =>  $sanitized['make_id'],
            'model_id' => $sanitized['model_id'],
            'generation_id' =>  $sanitized['generation_id'],
            'series_id' =>  $sanitized['series_id'],
            'trim_id' => $sanitized['trim_id'],
            'equipment_id' =>  $sanitized['equipment_id'],
            'additional_vehicle_info' =>  $sanitized['additional_vehicle_info'],
            'seats' =>  $sanitized['seats'],
            'fuel_consumption' =>  $sanitized['fuel_consumption'],
            'co2_emissions' => $sanitized['co2_emissions'],
            'latitude' =>  $sanitized['latitude'],
            'longitude' =>  $sanitized['longitude'],
            'geocoding_status' =>  $sanitized['geocoding_status'],
            
        ]);

        return ['data' => ['ad' => $ad,'auto_ad' =>$autoAd]];
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
