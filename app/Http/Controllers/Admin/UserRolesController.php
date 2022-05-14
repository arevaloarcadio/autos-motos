<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRole\BulkDestroyUserRole;
use App\Http\Requests\Admin\UserRole\DestroyUserRole;
use App\Http\Requests\Admin\UserRole\IndexUserRole;
use App\Http\Requests\Admin\UserRole\StoreUserRole;
use App\Http\Requests\Admin\UserRole\UpdateUserRole;
use App\Models\UserRole;
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

class UserRolesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserRole $request
     * @return array|Factory|View
     */
    public function index(IndexUserRole $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UserRole::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['user_id', 'role_id'],

            // set columns to searchIn
            ['user_id', 'role_id'],

            function ($query) use ($request) {
                        
                $columns =  ['user_id', 'role_id'];
                
                if ($request->filters) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }

                foreach (UserRole::getRelationships() as $key => $value) {
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
        $this->authorize('admin.user-role.create');

        return view('admin.user-role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRole $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUserRole $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the UserRole
        $userRole = UserRole::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/user-roles'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/user-roles');
    }

    /**
     * Display the specified resource.
     *
     * @param UserRole $userRole
     * @throws AuthorizationException
     * @return void
     */
    public function show(UserRole $userRole)
    {
        $this->authorize('admin.user-role.show', $userRole);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserRole $userRole
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UserRole $userRole)
    {
        $this->authorize('admin.user-role.edit', $userRole);


        return view('admin.user-role.edit', [
            'userRole' => $userRole,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRole $request
     * @param UserRole $userRole
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUserRole $request, UserRole $userRole)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values UserRole
        $userRole->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/user-roles'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUserRole $request
     * @param UserRole $userRole
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUserRole $request, UserRole $userRole)
    {
        $userRole->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUserRole $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUserRole $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    UserRole::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
