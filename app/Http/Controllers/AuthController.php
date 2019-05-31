<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
   
    	if(Auth::attempt(['username' => $request->username, 'password' => $request->password]))
		{
			return Auth::user();
		}
		else
		{
			dd("login gagal");
		}
    }

    public function register(Request $request)
    {
    	User::create($request->all());
    	dd("user created");
    }

    public function logout()
    {
    	auth()->logout();
    }

}
