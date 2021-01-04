<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateLogin;
use Illuminate\Support\Facades\Auth;
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

        $response = [
            'error' => false,
            'authenticate' => true,
            'access_token' => $data['access_token'],
            'user' => $data['user'],
            'message' => 'Login Success',
        ];

        return response()->json($response);

    
    }

    protected function respondWithToken($token)
    {
        $response = [
            'authenticate' => true,
            'access_token' => $token,
            'user' => auth('api')->user()
        ];

        return response()->json($response);
    }

    //if user not login or wrong token, then throw exception and return json error
    //else return user and token data
    public function isLogin()
    {
        $data = $this->authService->isLogin();

        $response = [
            'error' => false,
            'user' => $data['user'],
            'message' => 'Token is valid',
        ];

        return response()->json($response);
    }

    public function logout()
    {
        $this->authService->logout();
        
        $response = [
            'error' => false, 
            'message' => 'Successfully logged out'
        ];

        return response()->json($response);
        

        
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


}
