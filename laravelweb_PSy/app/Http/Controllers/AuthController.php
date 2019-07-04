<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\ValidateLogin;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    
    public function login(ValidateLogin $request)
    {
        
    	if(Auth::attempt(['username' => $request->username, 'password' => $request->password]))
		{

            return response()->json([
                'error' => false,
                'user' => Auth::user(),
                'message' => 'Login Success',
            ]);
			
		}
		else
		{
			return response()->json([
                'error' => false,
                'user' => Auth::user(),
                'message' => 'Login Failed'
            ]);
		}
    }

    public function isLogin()
    {
        //dd(Auth::user());
        $user = Auth::user();
        if(Auth::check()){
            return response()->json([
                'error' => false,
                'isLogin' => true,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'error' => false,
                'isLogin' => false,
                'user' => $user
            ]);
        }
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'error' => false,
            'message' => 'Logout Success',
        ]);

    	
    }

}
