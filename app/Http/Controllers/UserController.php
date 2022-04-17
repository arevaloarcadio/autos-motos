<?php

namespace App\Http\Controllers;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
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

    public function register(Request $request)
    {   
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'code_postal' => 'nullable|integer|min:4',
            'email' => 'required|string|unique:users|email|max:255',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->code_postal = $request->code_postal;
        $user->last_name = $request->last_name;
        $user->password = Hash::make($request->password);
        
        $user->save();

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'), 201);
    }

    public function register_professional(Request $request)
    {   
        $resource = ApiHelper::resource();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'code_postal' => 'nullable|integer|min:4',
            'email' => 'required|string|unique:users|email|max:255',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            ApiHelper::setError($resource, 0, 422, $validator->errors()->all());
            return $this->sendResponse($resource);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->code_postal = $request->code_postal;
        $user->last_name = $request->last_name;
        $user->password = Hash::make($request->password);
        
        $user->save();

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'), 201);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['status' => 'success', 'message' => 'logout'], 200);
    }
}
