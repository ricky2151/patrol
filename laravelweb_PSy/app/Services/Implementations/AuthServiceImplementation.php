<?php

namespace App\Services\Implementations;
use App\Services\Contracts\AuthServiceContract;
use App\Exceptions\LoginFailedException;
use App\Repositories\Contracts\AuthRepositoryContract as AuthRepo;

class AuthServiceImplementation implements AuthServiceContract
{
    protected $authRepo;

    public function __construct(AuthRepo $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function login($request)
    {
        $credentials = [
            "username" => $request['username'],
            "password" => $request['password']
        ];
        if($this->authRepo->login($credentials))
        {
            if($request->isAdmin)
            {
                if($this->authRepo->canMePlayARole('Admin'))
                {
                    return response()->json([
                        'error' => false,
                        'authenticate' => true,
                        'access_token' => $token,
                        'user' => Auth::user(),
                        'message' => 'Login Success',
                    ]);        
                }
                else
                {
                    throw new LoginFailedException("Could not create token !");
                }
            }
            else
            {
                return response()->json([
                    'error' => false,
                    'authenticate' => true,
                    'access_token' => $token,
                    'user' => Auth::user(),
                    'message' => 'Login Success',
                ]);
            }
        }
        else
        {
            return false;
        }
    }
}