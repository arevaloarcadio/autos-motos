<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MotoAdOption\BulkDestroyMotoAdOption;
use App\Http\Requests\Admin\MotoAdOption\DestroyMotoAdOption;
use App\Http\Requests\Admin\MotoAdOption\IndexMotoAdOption;
use App\Http\Requests\Admin\MotoAdOption\StoreMotoAdOption;
use App\Http\Requests\Admin\MotoAdOption\UpdateMotoAdOption;
use App\Models\MotoAdOption;
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

class MotoAdOptionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMotoAdOption $request
     * @return array|Factory|View
     */
    public function index(IndexMotoAdOption $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MotoAdOption::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['moto_ad_id', 'option_id'],

            // set columns to searchIn
            ['moto_ad_id', 'option_id'],

            function ($query) use ($request) {
                        
                $columns =   ['moto_ad_id', 'option_id'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }
                
                foreach (MotoAdOption::getRelationships() as $key => $value) {
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
        $this->authorize('admin.moto-ad-option.create');

        return view('admin.moto-ad-option.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMotoAdOption $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMotoAdOption $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the MotoAdOption
        $motoAdOption = MotoAdOption::create($sanitized);

        return ['data' => $motoAdOption];
    }

    /**
     * Display the specified resource.
     *
     * @param MotoAdOption $motoAdOption
     * @throws AuthorizationException
     * @return void
     */
    public function show(MotoAdOption $motoAdOption)
    {
        $this->authorize('admin.moto-ad-option.show', $motoAdOption);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MotoAdOption $motoAdOption
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MotoAdOption $motoAdOption)
    {
        $this->authorize('admin.moto-ad-option.edit', $motoAdOption);


        return view('admin.moto-ad-option.edit', [
            'motoAdOption' => $motoAdOption,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMotoAdOption $request
     * @param MotoAdOption $motoAdOption
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMotoAdOption $request, MotoAdOption $motoAdOption)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MotoAdOption
        $motoAdOption->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/moto-ad-options'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/moto-ad-options');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMotoAdOption $request
     * @param MotoAdOption $motoAdOption
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMotoAdOption $request, MotoAdOption $motoAdOption)
    {
        $motoAdOption->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMotoAdOption $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMotoAdOption $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MotoAdOption::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
