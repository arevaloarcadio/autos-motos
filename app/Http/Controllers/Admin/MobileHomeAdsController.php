<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MobileHomeAd\BulkDestroyMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\DestroyMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\IndexMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\StoreMobileHomeAd;
use App\Http\Requests\Admin\MobileHomeAd\UpdateMobileHomeAd;
use App\Models\{Ad,DealerShowRoom,MobileHomeAd};
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MobileHomeAdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMobileHomeAd $request
     * @return array|Factory|View
     */
    public function index(IndexMobileHomeAd $request)
    {
        if ($request->all) {
            
            $query = MobileHomeAd::query();

            $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'length_cm', 'width_cm', 'height_cm', 'max_weight_allowed_kg', 'payload_kg', 'engine_displacement', 'mileage', 'power_kw', 'axes', 'seats', 'sleeping_places', 'beds', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (MobileHomeAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MobileHomeAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'length_cm', 'width_cm', 'height_cm', 'max_weight_allowed_kg', 'payload_kg', 'engine_displacement', 'mileage', 'power_kw', 'axes', 'seats', 'sleeping_places', 'beds', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            // set columns to searchIn
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'beds', 'emission_class', 'condition', 'color', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],
            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'vehicle_category_id', 'transmission_type_id', 'construction_year', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'owners', 'length_cm', 'width_cm', 'height_cm', 'max_weight_allowed_kg', 'payload_kg', 'engine_displacement', 'mileage', 'power_kw', 'axes', 'seats', 'sleeping_places', 'beds', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
                
                    if ($request->filters) {
                        foreach ($columns as $column) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (MobileHomeAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.mobile-home-ad.create');

        return view('admin.mobile-home-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMobileHomeAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMobileHomeAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        $ad = Ad::create([
            'slug' => Str::slug($sanitized['title']),
            'title' => $sanitized['title'],
            'description' => $sanitized['description'],
            'thumbnail' => $sanitized['thumbnail'],
            'status' => 0,
            'type' => 'mobile-home',
            'is_featured' => 0,
            'user_id' => Auth::user()->id,
            'market_id' => $sanitized['market_id'],
            'external_id' =>null,
            'source' => null,
            'images_processing_status' => 'SUCCESSFUL',
            'images_processing_status_text' => null,
        ]);
        
        $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id']  : null;
        // Store the MobileHomeAd
        $mobileHomeAd = MobileHomeAd::create([
            'ad_id' =>  $ad->id,
            'make_id' => $sanitized['make_id'],
            'custom_make' => $sanitized['custom_make'],
            'model_id' => $sanitized['model_id'],
            'custom_model' => $sanitized['custom_model'],
            'fuel_type_id' =>$sanitized['fuel_type_id'],
            'vehicle_category_id' => $sanitized['vehicle_category_id'],
            'transmission_type_id' => $sanitized['transmission_type_id'],
            'construction_year' => $sanitized['construction_year'],
            'first_registration_month' =>$sanitized['first_registration_month'],
            'first_registration_year' => $sanitized['first_registration_year'],
            'inspection_valid_until_month' => $sanitized['inspection_valid_until_month'],
            'inspection_valid_until_year' => $sanitized['inspection_valid_until_year'],
            'owners' => $sanitized['owners'],
            'length_cm' => $sanitized['length_cm'],
            'width_cm' => $sanitized['width_cm'],
            'height_cm' => $sanitized['height_cm'],
            'max_weight_allowed_kg' => $sanitized['max_weight_allowed_kg'],
            'payload_kg' => $sanitized['payload_kg'],
            'engine_displacement' => $sanitized['engine_displacement'],
            'mileage' => $sanitized['mileage'],
            'power_kw' => $sanitized['power_kw'],
            'axes' => $sanitized['axes'],
            'seats' => $sanitized['seats'],
            'sleeping_places' => $sanitized['sleeping_places'],
            'beds' => $sanitized['beds'],
            'emission_class' => $sanitized['emission_class'],
            'fuel_consumption' => $sanitized['fuel_consumption'],
            'co2_emissions' => $sanitized['co2_emissions'],
            'condition' => $sanitized['condition'],
            'color' => $sanitized['color'],
            'price' => $sanitized['price'],
            'price_contains_vat' =>$sanitized['price_contains_vat'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' => $sanitized['first_name'],
            'last_name' => $sanitized['last_name'],
            'email_address' =>$sanitized['email_address'],
            'address' => $sanitized['address'],
            'zip_code' => $sanitized['zip_code'],
            'city' => $sanitized['city'],
            'country' => $sanitized['country'],
            'mobile_number' =>$sanitized['mobile_number'],
            'landline_number' => $sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' => $sanitized['youtube_link']
        ]);

        return ['data' => ['ad' => $ad, 'mobile_home' => $mobileHomeAd]];
    }

    /**
     * Display the specified resource.
     *
     * @param MobileHomeAd $mobileHomeAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(MobileHomeAd $mobileHomeAd)
    {
        $this->authorize('admin.mobile-home-ad.show', $mobileHomeAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MobileHomeAd $mobileHomeAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MobileHomeAd $mobileHomeAd)
    {
        $this->authorize('admin.mobile-home-ad.edit', $mobileHomeAd);


        return view('admin.mobile-home-ad.edit', [
            'mobileHomeAd' => $mobileHomeAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMobileHomeAd $request
     * @param MobileHomeAd $mobileHomeAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMobileHomeAd $request, MobileHomeAd $mobileHomeAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MobileHomeAd
        $mobileHomeAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/mobile-home-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/mobile-home-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMobileHomeAd $request
     * @param MobileHomeAd $mobileHomeAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMobileHomeAd $request, MobileHomeAd $mobileHomeAd)
    {
        $mobileHomeAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMobileHomeAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMobileHomeAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MobileHomeAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
