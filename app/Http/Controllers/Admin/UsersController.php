<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\BulkDestroyUser;
use App\Http\Requests\Admin\User\DestroyUser;
use App\Http\Requests\Admin\User\IndexUser;
use App\Http\Requests\Admin\User\StoreUser;
use App\Http\Requests\Admin\User\UpdateUser;
use App\Models\{Ad,User};
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\NewUser;

class UsersController extends Controller
{
    use ApiController;
    /**
     * Display a listing of the resource.
     *
     * @param IndexUser $request
     * @return array|Factory|View
     */
    public function index(IndexUser $request)
    {
        if ($request->all) {
            
            $query = User::query();

            $columns = ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'email_verified_at', 'type' , 'image' ,'status','dealer_id'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

            foreach (User::getRelationships() as $key => $value) {
               $query->with($key);
            }

            return ['data' => $query->get()];
        }
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(User::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'email_verified_at', 'type', 'status','dealer_id'],

            // set columns to searchIn
            ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'type','status','dealer_id'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'email_verified_at', 'type' , 'image' ,'status','dealer_id'];
                
            if ($request->filters) {
                foreach ($columns as $column) {
                    foreach ($request->filters as $key => $filter) {
                        if ($column == $key) {
                           $query->where($key,$filter);
                        }
                    }
                }
            }

                if ($request->dateStart && $request->dateEnd) {
                     $query->whereBetween('created_at',[$request->dateStart,$request->dateEnd]);
                }

                if ($request->dateStart && !$request->dateEnd) {
                     $query->where('created_at','LIKE','%'.$request->dateStart.'%');
                }     

                foreach (User::getRelationships() as $key => $value) {
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
        $this->authorize('admin.user.create');

        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUser $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUser $request)
    {

        $resource = ApiHelper::resource();

        try {
            // Sanitize input
            $sanitized = $request->getSanitized();
            $sanitized['password'] = Hash::make($sanitized['password']);
            $sanitized['status'] = 'Pendiente';
            $sanitized['image'] = 'users/user-default-ocassional.png';
            $sanitized['type'] =  array_key_exists('dealer_id', $sanitized) ? 'Profesional' : 'Ocasional';
            // Store the User
            $user = User::create($sanitized);

            $user->notify(new NewUser($user));
            
            return response()->json(['data' => $user], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @throws AuthorizationException
     * @return void
     */
    public function show(User $user)
    {
        return ['data' => $user];
    }

    public function setStatus(Request $request, User $user)
    {
        $user->status = $request->status;
        $user->save();
        
        return ['data' => $user];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(User $user)
    {
        $this->authorize('admin.user.edit', $user);


        return view('admin.user.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUser $request
     * @param User $user
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUser $request, User $user)
    {
        $resource = ApiHelper::resource();

        try {
            // Sanitize input
            $data = [];
            
            $sanitized = $request->getSanitized();

            $data['first_name'] = $sanitized['first_name'] ?? $user['first_name']  ;
            $data['last_name'] = $sanitized['last_name'] ?? $user['last_name'];
            $data['mobile_number'] = $sanitized['mobile_number'] ?? $user['mobile_number'];
            $data['landline_number'] = $sanitized['landline_number'] ?? $user['landline_number'];
            $data['whatsapp_number'] = $sanitized['whatsapp_number'] ?? $user['whatsapp_number'];
            $data['email'] =  $sanitized['email'] ?? $user['email'];
            $data['password'] = array_key_exists('password', $sanitized) ? Hash::make($sanitized['password']) : $user['password'];
            $data['dealer_id'] = $sanitized['dealer_id'] ?? $user['dealer_id'];
            
            $data['image'] = $request->file('image') ? $this->uploadFile($request->file('image'),$user->id) : $user->image;
            // Update changed values User
            $user->update($data);

            return response()->json(['data' => $user], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function updateProfile(Request $request)
    {
        $resource = ApiHelper::resource();
       
        $user = Auth::user();
       
        $validator = Validator::make($request->all(), [
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'image' => ['nullable'],
            'email' => 'nullable|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', 'min:7', 'string'],
            'dealer_id' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }
        
        try {  
            $data = [];
            $data['first_name'] = $request['first_name'] ?? $user['first_name'];
            $data['last_name'] = $request['last_name'] ?? $user['last_name'];
            $data['mobile_number'] = $request['mobile_number'] ?? $user['mobile_number'];
            $data['landline_number'] = $request['landline_number'] ?? $user['landline_number'];
            $data['whatsapp_number'] = $request['whatsapp_number'] ?? $user['whatsapp_number'];
            $data['email'] =  $request['email'] ?? $user['email'];
            $data['password'] =  $request['password'] ? Hash::make($request['password']) : $user['password'];
            $data['dealer_id'] = $request['dealer_id'] ?? $user['dealer_id'];
            
            $data['image'] = $request->file('image') ? $this->uploadFile($request->file('image'),$user->id) : $user->image;
            
            $user->update($data);

            return response()->json(['data' => $user], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function getDealer()
    {
        $resource = ApiHelper::resource();

        $user = Auth::user();


        try {

            $dealer = $user->dealer;
           
            return response()->json(['data' => $dealer], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function countAdsByUser(User $user)
    {
        $resource = ApiHelper::resource();

        try {

            $product_ads = Ad::where('user_id',$user->id)
                ->whereIn('type',['auto','mobile-home','truck','moto'])
                ->count();

            $service_ads = Ad::where('user_id',$user->id)
                ->whereIn('type',['shop','rental','mechanic'])
                ->count();

            return response()->json(['data' => ['product_ads' =>$product_ads, 'service_ads' => $service_ads]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function updateOcassional(Request $request)
    {
        $resource = ApiHelper::resource();

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => ['sometimes', 'confirmed', 'min:7', 'string'],
        //    'image' => ['file'],
        //    'dealer_id' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {

            $sanitized['image'] = $this->uploadFile($request->file('image'),$user->id);
          
            $user->update($request->all());
           
           
            return response()->json(['data' => $user], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUser $request
     * @param User $user
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUser $request, User $user)
    {
        $user->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUser $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUser $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    User::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function confirm_email($email)
    {
        if ($email) {

            $user = User::where('email',$email)->first();

            if ($user->email_verified_at == null) {
                $user->email_verified_at = Carbon::now();
                $user->save();   
            }
        }
        
        return view('landing.confirmar');
    }

    public function uploadFile($file,$id)
    {   
        $path = null;
        
        if ($file) {
            $path = $file->store(
                'users/'.$id, 's3'
            );
        }
        
        return $path;
    }
}
