<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateLogin;
use Illuminate\Support\Facades\Auth;
use App\Services\Contracts\AuthServiceContract as AuthService;
use App\Exceptions\LoginFailedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

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
        try {
            $data = $this->authService->login($validatedRequest);

            $response = [
                'error' => false,
                'authenticate' => true,
                'access_token' => $data['access_token'],
                'user' => $data['user'],
                'message' => 'Login Success',
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            if ( $th instanceof LoginFailedException) {
                throw $th;
            } else {
                throw new LoginFailedException('Login Failed : Undefined Error');
            }
        }
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
        try {
            $data = $this->authService->isLogin();

            $response = [
                'error' => false,
                'user' => $data['user'],
                'message' => 'Token is valid',
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            if($th instanceof LoginFailedException || $th instanceof TokenInvalidException || $th instanceof TokenExpiredException
                || $th instanceof TokenBlacklistedException || $th instanceof JWTException) {
                throw $th;
            } else {
                throw new LoginFailedException('Login Failed : Undefined Error');
            }
            
        }
    }

    public function logout()
    {
        try {
            $this->authService->logout();
        
            $response = [
                'error' => false, 
                'message' => 'Successfully logged out'
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            if($th instanceof LoginFailedException || $th instanceof TokenInvalidException || $th instanceof TokenExpiredException
                || $th instanceof TokenBlacklistedException || $th instanceof JWTException) {
                throw $th;
            } else {
                throw new LogoutFailedException('Logout Failed : Undefined Error');
            }
        }
        
        

        
    }

    

}
