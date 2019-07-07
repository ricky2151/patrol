<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

    	if(Auth::check())
    	{
    		$user = User::find(auth()->user()->id);
    		if($user->canPlayARole('Admin'))
    		{
    			return $next($request);	
    		}
    	}

        return response()->json([
            'error' => true,
            'message' => 'Your are not admin',
        ]);
        	
        
    }
}
