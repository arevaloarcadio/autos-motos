<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MotoAd\BulkDestroyMotoAd;
use App\Http\Requests\Admin\MotoAd\DestroyMotoAd;
use App\Http\Requests\Admin\MotoAd\IndexMotoAd;
use App\Http\Requests\Admin\MotoAd\StoreMotoAd;
use App\Http\Requests\Admin\MotoAd\UpdateMotoAd;
use App\Models\{Ad,MotoAd,DealerShowRoom};
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MotoAdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMotoAd $request
     * @return array|Factory|View
     */
    public function index(IndexMotoAd $request)
    {
        if ($request->all) {
            
            $query = MotoAd::query();

            $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'last_customer_service_month', 'last_customer_service_year', 'owners', 'weight_kg', 'engine_displacement', 'mileage', 'power_kw', 'gears', 'cylinders', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (MotoAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MotoAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'last_customer_service_month', 'last_customer_service_year', 'owners', 'weight_kg', 'engine_displacement', 'mileage', 'power_kw', 'gears', 'cylinders', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            // set columns to searchIn
            ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'emission_class', 'condition', 'color', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'make_id', 'custom_make', 'model_id', 'custom_model', 'fuel_type_id', 'body_type_id', 'transmission_type_id', 'drive_type_id', 'first_registration_month', 'first_registration_year', 'inspection_valid_until_month', 'inspection_valid_until_year', 'last_customer_service_month', 'last_customer_service_year', 'owners', 'weight_kg', 'engine_displacement', 'mileage', 'power_kw', 'gears', 'cylinders', 'emission_class', 'fuel_consumption', 'co2_emissions', 'condition', 'color', 'price', 'price_contains_vat', 'dealer_id', 'dealer_show_room_id', 'first_name', 'last_name', 'email_address', 'zip_code', 'city', 'country', 'mobile_number', 'landline_number', 'whatsapp_number', 'youtube_link'];
                
                
                    if ($request->filters) {
                        foreach ($columns as $column) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (MotoAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.moto-ad.create');

        return view('admin.moto-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMotoAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMotoAd $request)
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
        // Store the MotoAd
        $motoAd = MotoAd::create([
            'ad_id' =>  $ad->id,
            'make_id' => $sanitized['make_id'],
            'custom_make' => $sanitized['custom_make'],
            'model_id' => $sanitized['model_id'],
            'custom_model' => $sanitized['custom_model'],
            'fuel_type_id' => $sanitized['fuel_type_id'],
            'body_type_id' => $sanitized['body_type_id'],
            'transmission_type_id' => $sanitized['transmission_type_id'],
            'drive_type_id' => $sanitized['drive_type_id'],
            'first_registration_month' => $sanitized['first_registration_month'],
            'first_registration_year' => $sanitized['first_registration_year'],
            'inspection_valid_until_month' => $sanitized['inspection_valid_until_month'],
            'inspection_valid_until_year' => $sanitized['inspection_valid_until_year'],
            'last_customer_service_month' => $sanitized['last_customer_service_month'],
            'last_customer_service_year' => $sanitized['last_customer_service_year'],
            'owners' => $sanitized['owners'],
            'weight_kg' => $sanitized['weight_kg'],
            'engine_displacement' => $sanitized['engine_displacement'],
            'mileage' =>$sanitized['mileage'],
            'power_kw' => $sanitized['power_kw'],
            'gears' => $sanitized['gears'],
            'cylinders' => $sanitized['cylinders'],
            'emission_class' => $sanitized['emission_class'],
            'fuel_consumption' => $sanitized['fuel_consumption'],
            'co2_emissions' => $sanitized['co2_emissions'],
            'condition' =>$sanitized['condition'],
            'color' =>$sanitized['color'],
            'price' =>$sanitized['price'],
            'price_contains_vat' => $sanitized['price_contains_vat'],
            'dealer_id' => Auth::user()->dealer_id ?? null,
            'dealer_show_room_id' => $dealer_show_room_id,
            'first_name' => $sanitized['first_name'],
            'last_name' => $sanitized['last_name'],
            'email_address' => $sanitized['email_address'],
            'address' => $sanitized['address'],
            'zip_code' => $sanitized['zip_code'],
            'city' => $sanitized['city'],
            'country' =>$sanitized['country'],
            'mobile_number' => $sanitized['mobile_number'],
            'landline_number' =>$sanitized['landline_number'],
            'whatsapp_number' => $sanitized['whatsapp_number'],
            'youtube_link' =>$sanitized['youtube_link']
        ]);

        return ['data' => ['ad' => $ad, 'moto_ad' => $motoAd]];
    }

    /**
     * Display the specified resource.
     *
     * @param MotoAd $motoAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(MotoAd $motoAd)
    {
        $this->authorize('admin.moto-ad.show', $motoAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MotoAd $motoAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MotoAd $motoAd)
    {
        $this->authorize('admin.moto-ad.edit', $motoAd);


        return view('admin.moto-ad.edit', [
            'motoAd' => $motoAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMotoAd $request
     * @param MotoAd $motoAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMotoAd $request, MotoAd $motoAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MotoAd
        $motoAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/moto-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/moto-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMotoAd $request
     * @param MotoAd $motoAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMotoAd $request, MotoAd $motoAd)
    {
        $motoAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMotoAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMotoAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MotoAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
