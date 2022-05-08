<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Store\BulkDestroyStore;
use App\Http\Requests\Admin\Store\DestroyStore;
use App\Http\Requests\Admin\Store\IndexStore;
use App\Http\Requests\Admin\Store\StoreStore;
use App\Http\Requests\Admin\Store\UpdateStore;
use App\Models\{SellerStore,Store} ;
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
use Illuminate\Http\Request;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;

class StoresController extends Controller
{
    use ApiController;
    /**
     * Display a listing of the resource.
     *
     * @param IndexStore $request
     * @return array|Factory|View
     */
    public function index(IndexStore $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Store::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'email', 'phone', 'city', 'code_postal', 'whatsapp', 'country_id', 'user_id'],

            // set columns to searchIn
            ['id', 'name', 'email', 'phone', 'city', 'code_postal', 'whatsapp'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'name', 'email', 'phone', 'city', 'code_postal', 'whatsapp', 'country_id', 'user_id'];
                
                if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
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
    
        $this->authorize('admin.store.create');

        return view('admin.store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStore $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {   
        $validator = \Validator::make($request->all(),[
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'string'],
            'phone' => ['required', 'string'],
            'city' => ['required', 'string'],
            'code_postal' => ['required', 'string'],
            'whatsapp' => ['required', 'string'],
            'country' => ['required', 'string'],
        ],
        [
           
        ]);

        if($validator->fails()){
            return response()->json(['response' => 'error', 'data' => json_decode($validator->errors()->toJson())],422);  
        }

        try {

            $store = new Store; 
            
            $store->name = $request->input('name');
            $store->email = $request->input('email');
            $store->phone = $request->input('phone');
            $store->city = $request->input('city');
            $store->code_postal = $request->input('code_postal');
            $store->whatsapp = $request->input('whatsapp');
            $store->country = $request->input('country');

            $store->save();
            
            return response()->json(['response' => 'OK', 'data' => $store]);  
        } catch (Exception $e) {
            return response()->json(['response' => 'error', 'data' => $e->getMessage()],500);  
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Store $store
     * @throws AuthorizationException
     * @return void
     */
    public function show(Store $store)
    {
        $this->authorize('admin.store.show', $store);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Store $store
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Store $store)
    {
        $this->authorize('admin.store.edit', $store);


        return view('admin.store.edit', [
            'store' => $store,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStore $request
     * @param Store $store
     * @return array|RedirectResponse|Redirector
     */
    public function update(Request $request, Store $store)
    {
        // Sanitize input
        $validator = \Validator::make($request->all(),[
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', 'string'],
            'phone' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'code_postal' => ['sometimes', 'string'],
            'whatsapp' => ['sometimes', 'string'],
            'country_id' => ['sometimes', 'int'],
            'user_id' => 'sometimes|int|exists:users,id',
        ],
        [
           
        ]);

        if($validator->fails()){
            return response()->json(['response' => 'error', 'data' => $validator->errors()->all()],422);  
        }

        try {
        
            $store->update($request->all()); 
            
            return response()->json(['response' => 'OK', 'data' => $store]);  
        } catch (Exception $e) {
            return response()->json(['response' => 'error', 'data' => $e->getMessage()],500);  
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStore $request
     * @param Store $store
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStore $request, Store $store)
    {
        $store->delete();

        return response()->json(['response' => 'OK', 'data' => '']);  
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStore $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStore $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Store::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response()->json(['response' => 'OK', 'data' => '']);  
    }
}
