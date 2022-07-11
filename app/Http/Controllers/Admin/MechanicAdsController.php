<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MechanicAd\BulkDestroyMechanicAd;
use App\Http\Requests\Admin\MechanicAd\DestroyMechanicAd;
use App\Http\Requests\Admin\MechanicAd\IndexMechanicAd;
use App\Http\Requests\Admin\MechanicAd\StoreMechanicAd;
use App\Http\Requests\Admin\MechanicAd\UpdateMechanicAd;
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
use App\Models\{Ad,MechanicAd,DealerShowRoom};

class MechanicAdsController extends Controller
{
    use ApiController;
    
    /**
     * Display a listing of the resource.
     *
     * @param IndexMechanicAd $request
     * @return array|Factory|View
     */
    public function index(IndexMechanicAd $request)
    {
        if ($request->all) {
            
            $query = MechanicAd::query();

            $columns = ['id', 'internal_name', 'slug', 'domain', 'default_locale_id', 'icon', 'mobile_number', 'whatsapp_number', 'email_address'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (MechanicAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MechanicAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address', 'geocoding_status'],

            // set columns to searchIn
            ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address', 'geocoding_status'],

            function ($query) use ($request) {
                        
                $columns =   ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address', 'geocoding_status'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (MechanicAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.mechanic-ad.create');

        return view('admin.mechanic-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMechanicAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMechanicAd $request)
    {
        try {

            $sanitized = $request->getSanitized();

            $ad = Ad::create([
                'slug' => Str::slug($sanitized['title']),
                'title' => $sanitized['title'],
                'description' => $sanitized['description'],
               // 'thumbnail' => $sanitized['thumbnail'],
                'status' => 0,
                'type' => 'mechanic',
                'is_featured' => 0,
                'user_id' => Auth::user()->id,
                'market_id' => $sanitized['market_id'],
                'external_id' =>null,
                'source' => null,
                'images_processing_status' => 'SUCCESSFUL',
                'images_processing_status_text' => null,
            ]);
            
            $mechanicAd = MechanicAd::create([
                'ad_id' =>  $ad->id,
                'address' => $sanitized['address'],
                'latitude' => $sanitized['latitude'] ?? null,
                'longitude' => $sanitized['longitude'] ?? null,
                'zip_code' => $sanitized['zip_code'],
                'city' => $sanitized['city'],
                'country' =>$sanitized['country'],
                'mobile_number' => $sanitized['mobile_number'],
                'whatsapp_number' => $sanitized['whatsapp_number'],
                'website_url' => $sanitized['website_url'],
                'email_address' => $sanitized['email_address'],
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            return response()->json(['data' => ['ad' => $ad,'mechanic_ad' => $mechanicAd]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }
        

    /**
     * Display the specified resource.
     *
     * @param MechanicAd $mechanicAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(MechanicAd $mechanicAd)
    {
        $this->authorize('admin.mechanic-ad.show', $mechanicAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MechanicAd $mechanicAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MechanicAd $mechanicAd)
    {
        $this->authorize('admin.mechanic-ad.edit', $mechanicAd);


        return view('admin.mechanic-ad.edit', [
            'mechanicAd' => $mechanicAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMechanicAd $request
     * @param MechanicAd $mechanicAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMechanicAd $request, MechanicAd $mechanicAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MechanicAd
        $mechanicAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/mechanic-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/mechanic-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMechanicAd $request
     * @param MechanicAd $mechanicAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMechanicAd $request, MechanicAd $mechanicAd)
    {
        $mechanicAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMechanicAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMechanicAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MechanicAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
