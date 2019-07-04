<?php

namespace App\Http\Middleware;

use Closure;

class RoleSuperAdmin
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
        $user = User::find(auth()->user()->id);
        
        
        if($user->canPlayARole('Superadmin'))
            return $next($request);
        else
            return response()->json([
                'error' => true,
                'message' => 'Your are not admin',
            ]);
    }
}
