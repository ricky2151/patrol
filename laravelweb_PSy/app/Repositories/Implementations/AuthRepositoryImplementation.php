<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\AuthRepositoryContract;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\LoginFailedException;

class AuthRepositoryImplementation implements AuthRepositoryContract
{
    public function login($credentials)
    {
        try {
            if (! $token = auth()->attempt($credentials)) {
                throw new LoginFailedException("Wrong Credentials !");
           }
           else
           {
                return ["access_token" => $token, "user" => Auth::user()];
           }
        } catch (JWTException $e) { 
            throw new LoginFailedException("Could not create token !");
        } catch (LoginFailedException $e)
        {
            throw $e;
        } catch (\Throwable $th) {
            throw new LoginFailedException("There is problem in authentication server !");
        }
    }
    
    public function canMePlayARole($role)
    {
        $user = Auth::user();
        if($user != null)
        {
            $thisrole = $user->role->name;

            if($role == 'Guard' && ($thisrole == 'Guard' || $thisrole == 'Admin' || $thisrole == 'Superadmin'))
            {
                return true;
            }
            else if($role == 'Admin' && ($thisrole == 'Admin' || $thisrole == 'Superadmin'))
            {
                return true;
            }
            else if($role == 'Superadmin' && ($thisrole == 'Superadmin'))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        
    }
    
}