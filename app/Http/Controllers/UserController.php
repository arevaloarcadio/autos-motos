<?php

namespace App\Http\Controllers;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Models\{SellerStore,User,UserRole,tore,Company};
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;

class UserController extends Controller
{
    const OVERRIDE_JWT_TTL = 4320;
    use ApiController;


    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            $credentials = $request->only('email', 'password');

            
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = Auth::user();
        
        $plan_active = $user->plan_active()->orderBy('created_at','DESC')->first();
    
        return response()->json([
            'token' => $token,
            'user'  => Auth::user(),
            'plan_active' => $plan_active
        ]);
    }

    public function authenticate_admin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            $admin = User::where('email',$credentials['email'])->first(); 
            
            $authenticate = false;
            
            if (is_null($admin)) {
                return response()->json(['error' => 'invalid_credentials'], Response::HTTP_UNAUTHORIZED);
            }

            foreach ($admin->roles as $role) {
                $authenticate = $role['name'] == 'ADMIN' ? true : false;
             
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], Response::HTTP_UNAUTHORIZED);
            }

            if (!$authenticate) {
                return response()->json(['error' => 'invalid_credentials'], Response::HTTP_UNAUTHORIZED);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = Auth::user();

        return response()->json([
            'token' => $token,
            'user'  => $user
        ]);
    }


    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

    public function register_occasional(Request $request)
    {   
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|min:7',
            'whatsapp_number' => 'required|string|min:7',
            'email' => 'required|string|unique:users|email|max:255',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }
        
        try {
            
            $user = new User;
            $user->first_name = $request->name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile_number = $request->mobile_number;
            $user->whatsapp_number = $request->whatsapp_number;
            $user->password = Hash::make($request->password);
            
            $user->save();

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'), 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }    
    }

    public function register_professional(Request $request)
    {   
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'code_postal' => 'nullable|integer|min:4',
            'email' => 'required|string|unique:users|email|max:255',
            'password' => 'required|min:6|confirmed',
            'store_id' => 'required|numeric|exists:stores,id',
            'company_id' => 'required|numeric|exists:companies,id',
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }

        try {
            
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->code_postal = $request->code_postal;
            $user->last_name = $request->last_name;
            $user->password = Hash::make($request->password);
            $user->company_id = $request->company_id;
            
            $user->save();

            $seller_store = new SellerStore;
            $seller_store->user_id = $user->id;
            $seller_store->store_id = $request->store_id;

            $seller_store->save(); 

            $store = Store::find($request->store_id);
            $company = Company::find($request->company_id);

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token','store','company'), 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['status' => 'success', 'message' => 'logout'], 200);
    }
}
