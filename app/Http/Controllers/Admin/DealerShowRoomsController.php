<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DealerShowRoom\BulkDestroyDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\DestroyDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\IndexDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\StoreDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\UpdateDealerShowRoom;
use App\Models\DealerShowRoom;
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

class DealerShowRoomsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDealerShowRoom $request
     * @return array|Factory|View
     */
    public function index(IndexDealerShowRoom $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(DealerShowRoom::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'email_address', 'mobile_number', 'landline_number', 'whatsapp_number', 'dealer_id', 'market_id'],

            // set columns to searchIn
            ['id', 'name', 'address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'email_address', 'mobile_number', 'landline_number', 'whatsapp_number', 'dealer_id', 'market_id'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'email_address', 'mobile_number', 'landline_number', 'whatsapp_number', 'dealer_id', 'market_id'];
                
                foreach ($columns as $column) {
                        if ($request->filters) {
                            foreach ($request->filters as $key => $filter) {
                                if ($column == $key) {
                                   $query->where($key,$filter);
                                }
                            }
                        }
                    }

                foreach (DealerShowRoom::getRelationships() as $key => $value) {
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
        $this->authorize('admin.dealer-show-room.create');

        return view('admin.dealer-show-room.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDealerShowRoom $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDealerShowRoom $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the DealerShowRoom
        $dealerShowRoom = DealerShowRoom::create($sanitized);

        return ['data' => $dealerShowRoom];
    }

    /**
     * Display the specified resource.
     *
     * @param DealerShowRoom $dealerShowRoom
     * @throws AuthorizationException
     * @return void
     */
    public function show(DealerShowRoom $dealerShowRoom)
    {
        $this->authorize('admin.dealer-show-room.show', $dealerShowRoom);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DealerShowRoom $dealerShowRoom
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(DealerShowRoom $dealerShowRoom)
    {
        $this->authorize('admin.dealer-show-room.edit', $dealerShowRoom);


        return view('admin.dealer-show-room.edit', [
            'dealerShowRoom' => $dealerShowRoom,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDealerShowRoom $request
     * @param DealerShowRoom $dealerShowRoom
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDealerShowRoom $request, DealerShowRoom $dealerShowRoom)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values DealerShowRoom
        $dealerShowRoom->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/dealer-show-rooms'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/dealer-show-rooms');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDealerShowRoom $request
     * @param DealerShowRoom $dealerShowRoom
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDealerShowRoom $request, DealerShowRoom $dealerShowRoom)
    {
        $dealerShowRoom->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDealerShowRoom $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDealerShowRoom $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DealerShowRoom::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
