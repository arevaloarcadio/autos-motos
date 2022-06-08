<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RentalAd\BulkDestroyRentalAd;
use App\Http\Requests\Admin\RentalAd\DestroyRentalAd;
use App\Http\Requests\Admin\RentalAd\IndexRentalAd;
use App\Http\Requests\Admin\RentalAd\StoreRentalAd;
use App\Http\Requests\Admin\RentalAd\UpdateRentalAd;
use App\Models\RentalAd;
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

class RentalAdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexRentalAd $request
     * @return array|Factory|View
     */
    public function index(IndexRentalAd $request)
    {
        if ($request->all) {
            
            $query = RentalAd::query();

            $columns = ['id', 'ad_id', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (RentalAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(RentalAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address'],

            // set columns to searchIn
            ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (RentalAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.rental-ad.create');

        return view('admin.rental-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRentalAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreRentalAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the RentalAd
        $rentalAd = RentalAd::create($sanitized);

        return ['data' => $rentalAd];
    }

    /**
     * Display the specified resource.
     *
     * @param RentalAd $rentalAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(RentalAd $rentalAd)
    {
        $this->authorize('admin.rental-ad.show', $rentalAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param RentalAd $rentalAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(RentalAd $rentalAd)
    {
        $this->authorize('admin.rental-ad.edit', $rentalAd);


        return view('admin.rental-ad.edit', [
            'rentalAd' => $rentalAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRentalAd $request
     * @param RentalAd $rentalAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateRentalAd $request, RentalAd $rentalAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values RentalAd
        $rentalAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/rental-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/rental-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRentalAd $request
     * @param RentalAd $rentalAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyRentalAd $request, RentalAd $rentalAd)
    {
        $rentalAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyRentalAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyRentalAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    RentalAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
