<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsersFavouriteAdSearch\BulkDestroyUsersFavouriteAdSearch;
use App\Http\Requests\Admin\UsersFavouriteAdSearch\DestroyUsersFavouriteAdSearch;
use App\Http\Requests\Admin\UsersFavouriteAdSearch\IndexUsersFavouriteAdSearch;
use App\Http\Requests\Admin\UsersFavouriteAdSearch\StoreUsersFavouriteAdSearch;
use App\Http\Requests\Admin\UsersFavouriteAdSearch\UpdateUsersFavouriteAdSearch;
use App\Models\UsersFavouriteAdSearch;
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

class UsersFavouriteAdSearchesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUsersFavouriteAdSearch $request
     * @return array|Factory|View
     */
    public function index(IndexUsersFavouriteAdSearch $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UsersFavouriteAdSearch::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            [''],

            // set columns to searchIn
            ['']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.users-favourite-ad-search.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.users-favourite-ad-search.create');

        return view('admin.users-favourite-ad-search.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUsersFavouriteAdSearch $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUsersFavouriteAdSearch $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the UsersFavouriteAdSearch
        $usersFavouriteAdSearch = UsersFavouriteAdSearch::create($sanitized);

        return ['data' => $usersFavouriteAdSearch];
    }

    /**
     * Display the specified resource.
     *
     * @param UsersFavouriteAdSearch $usersFavouriteAdSearch
     * @throws AuthorizationException
     * @return void
     */
    public function show(UsersFavouriteAdSearch $usersFavouriteAdSearch)
    {
        $this->authorize('admin.users-favourite-ad-search.show', $usersFavouriteAdSearch);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UsersFavouriteAdSearch $usersFavouriteAdSearch
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UsersFavouriteAdSearch $usersFavouriteAdSearch)
    {
        $this->authorize('admin.users-favourite-ad-search.edit', $usersFavouriteAdSearch);


        return view('admin.users-favourite-ad-search.edit', [
            'usersFavouriteAdSearch' => $usersFavouriteAdSearch,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUsersFavouriteAdSearch $request
     * @param UsersFavouriteAdSearch $usersFavouriteAdSearch
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUsersFavouriteAdSearch $request, UsersFavouriteAdSearch $usersFavouriteAdSearch)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values UsersFavouriteAdSearch
        $usersFavouriteAdSearch->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/users-favourite-ad-searches'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/users-favourite-ad-searches');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUsersFavouriteAdSearch $request
     * @param UsersFavouriteAdSearch $usersFavouriteAdSearch
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUsersFavouriteAdSearch $request, UsersFavouriteAdSearch $usersFavouriteAdSearch)
    {
        $usersFavouriteAdSearch->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUsersFavouriteAdSearch $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUsersFavouriteAdSearch $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    UsersFavouriteAdSearch::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
