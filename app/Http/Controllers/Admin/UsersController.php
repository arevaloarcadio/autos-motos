<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\BulkDestroyUser;
use App\Http\Requests\Admin\User\DestroyUser;
use App\Http\Requests\Admin\User\IndexUser;
use App\Http\Requests\Admin\User\StoreUser;
use App\Http\Requests\Admin\User\UpdateUser;
use Illuminate\Validation\Rule;
use App\Models\{Ad,User,Dealer,DealerShowRoom};
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

            $columns = ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'email_verified_at', 'type' , 'image' ,'status','code_postal','dealer_id','address','country','city'];
                
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
            ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'email_verified_at', 'type' , 'image' ,'status','dealer_id','code_postal','address','country','city','created_at'],

            // set columns to searchIn
            ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'email_verified_at', 'type' , 'image' ,'status','dealer_id','code_postal','address','country','city','created_at'],

            function ($query) use ($request) {
                        
                $columns =  ['id', 'first_name', 'last_name', 'mobile_number', 'landline_number', 'whatsapp_number', 'email', 'email_verified_at', 'type' , 'image' ,'status','dealer_id','code_postal','address','country','city','created_at'];
                
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

                if ($request->filter_like) {
                    
                    $filter =  $request->filter_like;
                    
                    $query->where(function ($query1) use ($filter){
                        $query1->where('first_name','LIKE','%'.$filter.'%')
                            ->orWhere('last_name','LIKE','%'.$filter.'%')
                            ->orWhere('email','LIKE','%'.$filter.'%')
                            ->orWhereRaw("dealer_id in(select id from dealers where company_name like '%".$filter."%')");
                    });
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

    public function validator_email(Request $request)
    {
        $resource = ApiHelper::resource();
        
        $validator = \Validator::make($request->all(), [
            'user_email' => ['required', 'email', Rule::unique('users', 'email'), 'string'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        return response()->json(['data' => 'OK'], 200);
    }

    public function validator_company_name(Request $request)
    {
        $resource = ApiHelper::resource();
        
        $validator = \Validator::make($request->all(), [
            'dealer_company_name' => ['required', Rule::unique('dealers', 'company_name'), 'string']
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        return response()->json(['data' => 'OK'], 200);
    }
    
    public function validator_dealer_show_room_name(Request $request)
    {
        $resource = ApiHelper::resource();
        
        $validator = \Validator::make($request->all(), [
            'dealer_show_room_name' => ['required', Rule::unique('dealer_show_rooms', 'name'), 'string']
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        return response()->json(['data' => 'OK'], 200);
    }

    public function store_professional(Request $request)
    {
        $resource = ApiHelper::resource();
        
        $validator = \Validator::make($request->all(), [
            'user_first_name' => ['required', 'string'],
            'user_last_name' => ['required', 'string'],
            'user_mobile_number' => ['nullable', 'string'],
            'user_landline_number' => ['nullable', 'string'],
            'user_whatsapp_number' => ['nullable', 'string'],
            'user_email' => ['required', 'email', Rule::unique('users', 'email'), 'string'],
            //'user_image' => ['nullable'],
            'user_password' => ['required', 'confirmed', 'min:7', 'string'],
            //dealer
            'dealer_company_name' => ['required', Rule::unique('dealers', 'company_name'), 'string'],
            'dealer_vat_number' => ['nullable', 'string'],
            'dealer_address' => ['required', 'string'],
            'dealer_zip_code' => ['required', 'string'],
            'dealer_city' => ['required', 'string'],
            'dealer_country' => ['required', 'string'],
            'dealer_logo_path' => ['nullable', 'file'],
            'dealer_email_address' => ['required', 'email', 'string'],
            'dealer_phone_number' => ['required', 'string'],
            'dealer_description' => ['nullable', 'string'],
            'dealer_whatsapp_number' => ['nullable', 'string'],
            //dealer_show_room
            'dealer_show_room_name' => ['required', Rule::unique('dealer_show_rooms', 'name'), 'string'],
            'dealer_show_room_address' => ['required', 'string'],
            'dealer_show_room_zip_code' => ['required', 'string'],
            'dealer_show_room_city' => ['required', 'string'],
            'dealer_show_room_country' => ['required', 'string'],
            'dealer_show_room_latitude' => ['nullable', 'string'],
            'dealer_show_room_longitude' => ['nullable', 'string'],
            'dealer_show_room_email_address' => ['required', 'email', 'string'],
            'dealer_show_room_mobile_number' => ['required', 'string'],
            'dealer_show_room_landline_number' => ['nullable', 'string'],
            'dealer_show_room_whatsapp_number' => ['nullable', 'string'],
            'dealer_show_room_market_id' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors());
            return $this->sendResponse($resource);
        }

        try {
            
            $dealer = new Dealer;
            $dealer->company_name = $request['dealer_company_name'];
            $dealer->slug = Str::slug($request['dealer_company_name']);
            $dealer->vat_number = $request['dealer_vat_number'];
            $dealer->address = $request['dealer_address'];
            $dealer->zip_code = $request['dealer_zip_code'];
            $dealer->city = $request['dealer_city'];
            $dealer->country = $request['dealer_country'];
            $dealer->logo_path = $this->uploadFile($request->file('dealer_logo_path'),$request['dealer_company_name']);
            $dealer->email_address = $request['dealer_email_address'];
            $dealer->phone_number = $request['dealer_phone_number'];
            $dealer->description = $request['dealer_description'];
            $code = Dealer::whereRaw('code is not null')->count()+1;
            $dealer->code =  str_pad($code, 5, "0",STR_PAD_LEFT);
            $dealer->description = $request['dealer_description'];
            $dealer->save();

            $dealerShowRoom = new DealerShowRoom;
            $dealerShowRoom->name = $request['dealer_show_room_name'];
            $dealerShowRoom->address = $request['dealer_show_room_address'];
            $dealerShowRoom->zip_code = $request['dealer_show_room_zip_code'];
            $dealerShowRoom->city = $request['dealer_show_room_city'];
            $dealerShowRoom->country = $request['dealer_show_room_country'];
            $dealerShowRoom->latitude = $request['dealer_show_room_latitude'];
            $dealerShowRoom->longitude = $request['dealer_show_room_longitude'];
            $dealerShowRoom->email_address = $request['dealer_show_room_email_address'];
            $dealerShowRoom->mobile_number = $request['dealer_show_room_mobile_number'];
            $dealerShowRoom->landline_number = $request['dealer_show_room_landline_number'];
            $dealerShowRoom->whatsapp_number = $request['dealer_show_room_whatsapp_number'];
            $dealerShowRoom->market_id = $request['dealer_show_room_market_id'];
            $dealerShowRoom->dealer_id = $dealer->id;
            $dealerShowRoom->save();

            $user = new User;
            $user->first_name = $request['user_first_name'];
            $user->last_name = $request['user_last_name'];
            $user->mobile_number = $request['user_mobile_number'];
            $user->landline_number = $request['user_landline_number'];
            $user->whatsapp_number = $request['user_whatsapp_number'];
            $user->email = $request['user_email'];
            $user->password = Hash::make($request['user_password']);
            $user->status = 'Pendiente'; 
            $user->image = $dealer->logo_path;
            $user->type = 'Profesional';
            $user->dealer_id = $dealer->id;
            $user->save();

            $user->notify(new NewUser($user));
            
            return response()->json([
                'data' => [
                    'user' => $user, 
                    'dealer' => $dealer, 
                    'dealer_show_room' => $dealerShowRoom 
                ] 
            ], 200);

        } catch (Exception $e) {
            
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            isset($dealerShowRoom) ? $dealerShowRoom->delete() : false;
            isset($dealer) ? $dealer->delete(): false;
            isset($user) ? $user->delete(): false;
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
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

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @throws AuthorizationException
     * @return void
     */
    public function show(User $user)
    {
        $user->dealer;
        $user->payment_histories;
        $user['plan_active'] = $user->plan_active()->orderBy('created_at','DESC')->get();
        
        if (!is_null($user->dealer)) {
           $user->dealer->showRooms;
        }

        $user['payment_histories'] = $user->payment_histories()->orderBy('created_at','DESC')->get();

        if(count($user['payment_histories']) != 0)
            $user['last_pay_method'] = $user->payment_histories()->orderBy('created_at','DESC')->get()[0]['way_to_pay'];
        else
            $user['last_pay_method'] = null;
        
        return ['data' => $user];
    }

    public function setStatus(Request $request, User $user)
    {
        $user->status = $request->status;
        $user->save();
        
        $user->dealer;
        $user['plan_active'] = $user->plan_active()->orderBy('created_at','DESC')->get();
        
        if (!is_null($user->dealer)) {
           $user->dealer->showRooms;
        }

        $user['payment_histories'] = $user->payment_histories()->orderBy('created_at','DESC')->get();

        if(count($user['payment_histories']) != 0)
            $user['last_pay_method'] = $user->payment_histories()->orderBy('created_at','DESC')->get()[0]['way_to_pay'];
        else
            $user['last_pay_method'] = null;

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
            $data['code_postal'] = $request['code_postal'] ?? $user['code_postal'];
            $data['address'] = $request['address'] ?? $user['address'];
            $data['country'] = $request['country'] ?? $user['country'];
            $data['city'] = $request['city'] ?? $user['city'];
            $data['password'] = array_key_exists('password', $sanitized) ? Hash::make($sanitized['password']) : $user['password'];
            $data['dealer_id'] = $sanitized['dealer_id'] ?? $user['dealer_id'];
            
            $data['image'] = $request->file('image') ? $this->uploadFile($request->file('image'),$user->id) : $user->image;
            // Update changed values User
            $user->update($data);

            if (!is_null($user->dealer_id)) {
                $dealer = Dealer::find($user->dealer_id);
                $dealer->company_name = $request['company_name'] ;
                $dealer->email_address = $request['email_address']  ;
                $dealer->vat_number = $request['vat_number'] ;
                $dealer->description = $request['description']  ;
                $dealer->zip_code = $request['zip_code']  ;
                $dealer->phone_number = $request['phone_number']  ;
                $dealer->address = $request['address'] ;
                $dealer->city = $request['city'];
                $dealer->country = $request['country'] ;
                $dealer->logo_path = $request->file('logo_path') ? $this->uploadFile($request->file('logo_path'),$user->id) : $dealer->logo_path;
                $dealer->save();
            }
            
            $user->dealer;
            
            $user['plan_active'] = $user->plan_active()->orderBy('created_at','DESC')->get();
            
            if (!is_null($user->dealer)) {
               $user->dealer->showRooms;
            }
            
            $user['payment_histories'] = $user->payment_histories()->orderBy('created_at','DESC')->get();

            if(count($user['payment_histories']) != 0)
                $user['last_pay_method'] = $user->payment_histories()->orderBy('created_at','DESC')->get()[0]['way_to_pay'];
            else
                $user['last_pay_method'] = null;

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
            'code_postal' => ['sometimes', 'string'],
            'address' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
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
            $data['code_postal'] = $request['code_postal'] ?? $user['code_postal'];
            $data['address'] = $request['address'] ?? $user['address'];
            $data['country'] = $request['country'] ?? $user['country'];
            $data['city'] = $request['city'] ?? $user['city'];
            $data['email'] =  $request['email'] ?? $user['email'];
            $data['password'] =  $request['password'] ? Hash::make($request['password']) : $user['password'];
            
            $data['image'] = $request->file('image') ? $this->uploadFile($request->file('image'),$user->id) : $user->image;
            
            $user->update($data);
            $user->dealer;
            
            if (!is_null($user->dealer)) {
               $user->dealer->showRooms;
            }
            
            $user['plan_active'] = $user->plan_active()->orderBy('created_at','DESC')->get();
            
            $user['payment_histories'] = $user->payment_histories()->orderBy('created_at','DESC')->get();

            if(count($user['payment_histories']) != 0)
                $user['last_pay_method'] = $user->payment_histories()->orderBy('created_at','DESC')->get()[0]['way_to_pay'];
            else
                $user['last_pay_method'] = null;

            return response()->json(['data' => $user], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function getUserInfo()
    {
        $resource = ApiHelper::resource();

        try {

            $user = Auth::user();
            $plan_active = $user->plan_active()->orderBy('created_at','DESC')->first();
            $user->dealer;
         

            if (!is_null($user->dealer)) {
               $user->dealer->showRooms;
            }
            
            
            $user['plan_active'] = $user->plan_active()->orderBy('created_at','DESC')->get();
            
            $user['payment_histories'] = $user->payment_histories()->orderBy('created_at','DESC')->get();

            if(count($user['payment_histories']) != 0)
                $user['last_pay_method'] = $user->payment_histories()->orderBy('created_at','DESC')->get()[0]['way_to_pay'];
            else
                $user['last_pay_method'] = null;

            return response()->json([
                    'data' => [
                        'user'  =>  $user,
                        'plan_active' => $plan_active
                    ]
                ], 200);

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
           
            if (!is_null($dealer)) {
               $dealer->showRooms;
            }

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

            return response()->json(['data' => ['product_ads' => $product_ads, 'service_ads' => $service_ads]], 200);

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
            'code_postal' => ['sometimes', 'string'],
            'address' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
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
            
            $user['plan_active'] = $user->plan_active()->orderBy('created_at','DESC')->get();

            $user['payment_histories'] = $user->payment_histories()->orderBy('created_at','DESC')->get();

            if(count($user['payment_histories']) != 0)
                $user['last_pay_method'] = $user->payment_histories()->orderBy('created_at','DESC')->get()[0]['way_to_pay'];
            else
                $user['last_pay_method'] = null;

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
            
            if (is_null($user)) {
               return view('confirmar');
            }

            if ($user->email_verified_at == null) {
                $user->email_verified_at = Carbon::now();
                $user->save();   
            }
        }
        
        return view('confirmar');
    }

   
}
