<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\BulkDestroyCompany;
use App\Http\Requests\Admin\Company\DestroyCompany;
use App\Http\Requests\Admin\Company\IndexCompany;
use App\Http\Requests\Admin\Company\StoreCompany;
use App\Http\Requests\Admin\Company\UpdateCompany;
use App\Models\Company;
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
use App\Traits\ApiController;
use App\Helpers\Api as ApiHelper;


class CompaniesController extends Controller
{
    use ApiController;
    /**
     * Display a listing of the resource.
     *
     * @param IndexCompany $request
     * @return array|Factory|View
     */
    public function index(IndexCompany $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Company::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'cif', 'phone', 'city', 'code_postal', 'whatsapp', 'logo', 'country_id', 'user_id'],

            // set columns to searchIn
            ['id', 'name', 'cif', 'phone', 'city', 'code_postal', 'whatsapp', 'logo', 'description']
        );

        if ($request->has('bulk')) {
            return [
                'bulkItems' => $data->pluck('id')
            ];
        }
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
        $this->authorize('admin.company.create');

        return view('admin.company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompany $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {   
        $resource = ApiHelper::resource();

        $validator = \Validator::make($request->all(),[
            'name' => ['required', 'string'],
            'cif' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'city' => ['required', 'string'],
            'code_postal' => ['required', 'string'],
            'whatsapp' => ['required', 'string'],
            'logo' => ['required', 'file'],
            'description' => ['required', 'string'],
            'country' => ['required', 'string']
        ],
        [
           
        ]);

        if($validator->fails()){
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }

        try {

            $file = $request->file('logo');
            $logo = date('dmY').'-'.$file->getClientOriginalName();
            $destinationPath = public_path('/logos');
            $move = $file->move($destinationPath, $logo);

            $company = new Company; 
            $company->name = $request->name;
            $company->cif = $request->cif;
            $company->phone = $request->phone;
            $company->city = $request->city;
            $company->code_postal = $request->code_postal;
            $company->whatsapp = $request->whatsapp;
            $company->country = $request->country;
            $company->description = $request->description;
            $company->logo = $logo;
            $company->save();

            return response()->json(['response' => 'OK', 'data' => $company]);  
        
        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Company $company
     * @throws AuthorizationException
     * @return void
     */
    public function show(Company $company)
    {
        $this->authorize('admin.company.show', $company);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Company $company
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Company $company)
    {
        $this->authorize('admin.company.edit', $company);


        return view('admin.company.edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCompany $request
     * @param Company $company
     * @return array|RedirectResponse|Redirector
     */
    public function update(Request $request, Company $company)
    {
        $validator = \Validator::make($request->all(),[
             'name' => ['required', 'string'],
            'cif' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'city' => ['required', 'string'],
            'code_postal' => ['required', 'string'],
            'whatsapp' => ['required', 'string'],
            'logo' => ['required', 'file'],
            'description' => ['required', 'string'],
            'country' => ['required', 'string']
        ],
        [
           
        ]);

        if($validator->fails()){
            return response()->json(['response' => 'error', 'data' => $validator->errors()->all()],422);  
        }

        try {
        
            $company->update($request->all()); 
            
            return response()->json(['response' => 'OK', 'data' => $company]);  
        } catch (Exception $e) {
            return response()->json(['response' => 'error', 'data' => $e->getMessage()],500);  
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCompany $request
     * @param Company $company
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCompany $request, Company $company)
    {
        $company->delete();

        return response()->json(['response' => 'OK', 'data' => '']);      
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCompany $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCompany $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Company::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
