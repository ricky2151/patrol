<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\ValidateLogin;
use Illuminate\Support\Facades\Auth;
// use Tymon\JWTAuth\Facades\JWTAuth;
// use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\Contracts\AuthServiceContract as AuthService;



class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(ValidateLogin $request)
    {
        $validatedRequest = $request->validated();
        

        $data = $this->authService->login($validatedRequest);

        return response()->json([
            'error' => false,
            'authenticate' => true,
            'access_token' => $data['access_token'],
            'user' => $data['user'],
            'message' => 'Login Success',
        ]);

    
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'authenticate' => true,
            'access_token' => $token,
            'user' => auth('api')->user()
        ]);
    }

    //if user not login or wrong token, then throw exception and return json error
    //else return user and token data
    public function isLogin()
    {
        $data = $this->authService->isLogin();

        return response()->json([
            'error' => false,
            'user' => $data['user'],
            'message' => 'Token is valid',
        ]);
    }

    public function logout()
    {
        $this->authService->logout();
        
        return response()->json([
            'error' => false, 
            'message' => 'Successfully logged out'
        ]);
        

        
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


}
