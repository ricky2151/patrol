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

        $user = $this->authRepo->login($credentials); //throw exception if failed

        //if user send key 'isAdmin' on request AND value of key 'isAdmin' is valid (true/false) //then
        if(array_key_exists('isAdmin', $request) && filter_var($request['isAdmin'], FILTER_VALIDATE_BOOLEAN) == true) 
        {
            if($this->authRepo->canMePlayARole('Admin'))
            {
                return ["access_token" => $user['access_token'], "user" => $user['user']];      
            }
            else
            {
                throw new LoginFailedException("You Are Not Admin !");
            }
        }
        else
        {
            return ["access_token" => $user['access_token'], "user" => $user['user']];

        }
        
    }

    public function isLogin() 
    {
        return $this->authRepo->isLogin();
    }

    public function canMePlayARoleAsAdmin()
    {
        return $this->authRepo->canMePlayARole('Admin');
    }

    public function canMePlayARoleAsGuard()
    {
        return $this->authRepo->canMePlayARole('Guard');
    }

    public function canMePlayARoleAsSuperAdmin()
    {
        return $this->authRepo->canMePlayARole('Superadmin');
    }

    public function logout()
    {
        return $this->authRepo->logout();
    }
}