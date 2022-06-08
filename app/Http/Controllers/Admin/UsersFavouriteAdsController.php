<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsersFavouriteAd\BulkDestroyUsersFavouriteAd;
use App\Http\Requests\Admin\UsersFavouriteAd\DestroyUsersFavouriteAd;
use App\Http\Requests\Admin\UsersFavouriteAd\IndexUsersFavouriteAd;
use App\Http\Requests\Admin\UsersFavouriteAd\StoreUsersFavouriteAd;
use App\Http\Requests\Admin\UsersFavouriteAd\UpdateUsersFavouriteAd;
use App\Models\UsersFavouriteAd;
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

class UsersFavouriteAdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUsersFavouriteAd $request
     * @return array|Factory|View
     */
    public function index(IndexUsersFavouriteAd $request)
    {

        if ($request->all) {
            
            $query = UsersFavouriteAd::query();

            $columns = ['user_id', 'ad_id'];
                
            foreach ($columns as $column) {
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (UsersFavouriteAd::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UsersFavouriteAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['user_id', 'ad_id'],

            // set columns to searchIn
            ['user_id', 'ad_id'],

            function ($query) use ($request) {
                        
                $columns =     ['user_id', 'ad_id'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (UsersFavouriteAd::getRelationships() as $key => $value) {
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
        $this->authorize('admin.users-favourite-ad.create');

        return view('admin.users-favourite-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUsersFavouriteAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUsersFavouriteAd $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the UsersFavouriteAd
        $usersFavouriteAd = UsersFavouriteAd::create($sanitized);

        return ['data' => $usersFavouriteAd];
    }

    /**
     * Display the specified resource.
     *
     * @param UsersFavouriteAd $usersFavouriteAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(UsersFavouriteAd $usersFavouriteAd)
    {
        $this->authorize('admin.users-favourite-ad.show', $usersFavouriteAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UsersFavouriteAd $usersFavouriteAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UsersFavouriteAd $usersFavouriteAd)
    {
        $this->authorize('admin.users-favourite-ad.edit', $usersFavouriteAd);


        return view('admin.users-favourite-ad.edit', [
            'usersFavouriteAd' => $usersFavouriteAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUsersFavouriteAd $request
     * @param UsersFavouriteAd $usersFavouriteAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUsersFavouriteAd $request, UsersFavouriteAd $usersFavouriteAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values UsersFavouriteAd
        $usersFavouriteAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/users-favourite-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/users-favourite-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUsersFavouriteAd $request
     * @param UsersFavouriteAd $usersFavouriteAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUsersFavouriteAd $request, UsersFavouriteAd $usersFavouriteAd)
    {
        $usersFavouriteAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUsersFavouriteAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUsersFavouriteAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    UsersFavouriteAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
