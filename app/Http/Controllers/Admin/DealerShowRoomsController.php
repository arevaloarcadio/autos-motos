<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DealerShowRoom\BulkDestroyDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\DestroyDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\IndexDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\StoreDealerShowRoom;
use App\Http\Requests\Admin\DealerShowRoom\UpdateDealerShowRoom;
use App\Models\{Dealer,DealerShowRoom};
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
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DealerShowRoomsController extends Controller
{

    use ApiController; 
    /**
     * Display a listing of the resource.
     *
     * @param IndexDealerShowRoom $request
     * @return array|Factory|View
     */
    public function index(IndexDealerShowRoom $request)
    {   
        if ($request->all) {
            
            $query = DealerShowRoom::query();

            $columns = ['id', 'name', 'address', 'zip_code', 'city', 'country', 'latitude', 'longitude', 'email_address', 'mobile_number', 'landline_number', 'whatsapp_number', 'dealer_id', 'market_id'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
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

            return ['data' => $query->get()];
        }
        
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

        $resource = ApiHelper::resource();

        try {
            
            // Sanitize input
            $sanitized = $request->getSanitized();

            // Store the DealerShowRoom
            $dealerShowRoom = DealerShowRoom::create($sanitized);
    
            return response()->json(['data' => $dealerShowRoom], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
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

    public function byDealerId($dealer_id)
    {
        
        $resource = ApiHelper::resource();

        try {
            
            $DealerShowRoom = DealerShowRoom::where('dealer_id',$dealer_id)
                ->with('dealer')
                ->first();
    
            return response()->json(['data' => $DealerShowRoom], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
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
        $resource = ApiHelper::resource();

        try {
            
            $sanitized = $request->getSanitized();

            $sanitized['name'] = $sanitized['company_name'];

            $dealerShowRoom->update($sanitized);
            
            $dealer = Dealer::where('id',$dealerShowRoom->dealer_id)->first();

            if ($request->file('logo_path')) {
                $sanitized['logo_path'] = $this->uploadFile($request->file('logo_path'),$sanitized['company_name']);
            }else{
                $sanitized['logo_path'] = $dealer['logo_path'];
            }
            
            $dealer->update([
                'company_name' => $sanitized['company_name'],
                'logo_path' => $sanitized['logo_path']
            ]);

            return response()->json(['data' => ['dealer_show_room' => $dealerShowRoom, 'dealer' => $dealer]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
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

    public function uploadFile($file,$name)
    {   
        $path = null;
        
        if ($file) {
            $path = $file->store(
                'dealers/'.Str::slug($name), 's3'
            );
        }
        
        return $path;
    }
}
