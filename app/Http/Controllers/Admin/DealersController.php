<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dealer\BulkDestroyDealer;
use App\Http\Requests\Admin\Dealer\DestroyDealer;
use App\Http\Requests\Admin\Dealer\IndexDealer;
use App\Http\Requests\Admin\Dealer\StoreDealer;
use App\Http\Requests\Admin\Dealer\UpdateDealer;
use App\Models\Dealer;
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

class DealersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDealer $request
     * @return array|Factory|View
     */
    public function index(IndexDealer $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Dealer::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'company_name', 'vat_number', 'address', 'zip_code', 'city', 'country', 'logo_path', 'email_address', 'phone_number', 'status', 'external_id', 'source'],

            // set columns to searchIn
            ['id', 'slug', 'company_name', 'vat_number', 'address', 'zip_code', 'city', 'country', 'logo_path', 'email_address', 'phone_number', 'description', 'source'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'slug', 'company_name', 'vat_number', 'address', 'zip_code', 'city', 'country', 'logo_path', 'email_address', 'phone_number', 'description', 'source'];
                
                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }

                foreach (Dealer::getRelationships() as $key => $value) {
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
        $this->authorize('admin.dealer.create');

        return view('admin.dealer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDealer $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDealer $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Dealer
        $dealer = Dealer::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/dealers'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/dealers');
    }

    /**
     * Display the specified resource.
     *
     * @param Dealer $dealer
     * @throws AuthorizationException
     * @return void
     */
    public function show(Dealer $dealer)
    {
        $this->authorize('admin.dealer.show', $dealer);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Dealer $dealer
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Dealer $dealer)
    {
        $this->authorize('admin.dealer.edit', $dealer);


        return view('admin.dealer.edit', [
            'dealer' => $dealer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDealer $request
     * @param Dealer $dealer
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDealer $request, Dealer $dealer)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Dealer
        $dealer->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/dealers'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/dealers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDealer $request
     * @param Dealer $dealer
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDealer $request, Dealer $dealer)
    {
        $dealer->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDealer $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDealer $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Dealer::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
