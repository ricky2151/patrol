<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\ValidateLogin;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;



class AuthController extends Controller
{
    
    public function login(ValidateLogin $request)
    {
        try {

            $credentials['username'] = $request->username;
            $credentials['password'] = $request->password;

            if (! $token = auth()->attempt($credentials)) {
                 return response()->json([
                    'error' => true,
                    'message' => 'Login Failed'
                ]);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return $this->respondWithToken($token);
        //sampe sini, sudah login
        if($request->isAdmin)
        {
            if(Auth::user()->canPlayARole('Admin'))
            {
                return response()->json([
                    'error' => false,
                    'user' => Auth::user(),
                    'message' => 'Login Success',
                ]);        
            }
        }
        else
        {
            return response()->json([
                'error' => false,
                'user' => Auth::user(),
                'message' => 'Login Success',
            ]);
        }
            

    
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'authenticate' => true,
            'access_token' => $token,
            'user' => auth('api')->user()
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    // public function isLogin()
    // {
    //     //dd(Auth::user());
    //     $user = Auth::user();
    //     if(Auth::check()){
    //         return response()->json([
    //             'error' => false,
    //             'isLogin' => true,
    //             'user' => $user,
    //         ]);
    //     }
    //     else
    //     {
    //         return response()->json([
    //             'error' => true,
    //             'isLogin' => false,
    //             'user' => $user
    //         ]);
    //     }
    // }

    // public function logout()
    // {
    //     auth()->logout();
    //     return response()->json([
    //         'error' => false,
    //         'message' => 'Logout Success',
    //     ]);

    	
    // }

}
