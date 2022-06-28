<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Ad\BulkDestroyAd;
use App\Http\Requests\Admin\Ad\DestroyAd;
use App\Http\Requests\Admin\Ad\IndexAd;
use App\Http\Requests\Admin\Ad\StoreAd;
use App\Http\Requests\Admin\Ad\UpdateAd;
use App\Models\Ad;
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

class AdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAd $request
     * @return array|Factory|View
     */
    public function index(IndexAd $request)
    {   
   
        if ($request->all) {
            
            $query = Ad::query();

            $columns =  ['id', 'slug', 'title', 'description', 'thumbnail', 'type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','name_csv'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (Ad::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Ad::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'thumbnail', 'status', 'type', 'is_featured', 'user_id', 'market_id', 'external_id', 'source', 'images_processing_status','name_csv'],

            // set columns to searchIn
            ['id', 'slug', 'title', 'description', 'thumbnail', 'type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','name_csv'],

        function ($query) use ($request) {
                     
                $columns =  ['id', 'slug', 'title', 'description', 'thumbnail', 'type', 'market_id', 'source', 'images_processing_status', 'images_processing_status_text','name_csv'];

                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (Ad::getRelationships() as $key => $value) {
                    $query->with($key);
                }
            }
        );

        foreach ($data as $key0 => $ad) {
            foreach (Ad::getRelationships() as $key1 => $ad_Relationship) {
                if ($ad[$key1] !== null) {
                    if (get_class($ad[$key1]) != 'Illuminate\Database\Eloquent\Collection') {
                        foreach ($ad[$key1]::getRelationships() as $key2 => $value) {
                            $ad[$key1][$key2] = $ad[$key1][$key2];
                        }
                    }
                }
            }      
        }

        
        return ['data' => $data];
    }

    public function bySource(Request $request)
    {

        $ads = Ad::selectRaw('count(*) as count,source')
            ->where('source','!=',NULL)
            ->where('source','!=','CSV')
            ->groupBy('source');
        
        if ($request->dateStart && $request->dateEnd) {
             $ads->whereBetween('created_at',[$request->dateStart,$request->dateEnd]);
        }

        return ['data' => $ads->get()];
    }
    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.ad.create');

        return view('admin.ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Ad
        $ad = Ad::create($sanitized);

        return ['data' => $ad];
    }

    /**
     * Display the specified resource.
     *
     * @param Ad $ad
     * @throws AuthorizationException
     * @return void
     */
    public function show(Ad $ad)
    {
        $this->authorize('admin.ad.show', $ad);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Ad $ad
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Ad $ad)
    {
        $this->authorize('admin.ad.edit', $ad);


        return view('admin.ad.edit', [
            'ad' => $ad,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAd $request
     * @param Ad $ad
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAd $request, Ad $ad)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Ad
        $ad->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAd $request
     * @param Ad $ad
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAd $request, Ad $ad)
    {
        $ad->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Ad::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
