<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Contracts\AuthServiceContract as AuthService;
use App\Exceptions\LoginFailedException;

class RoleSuperAdmin
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($this->authService->canMePlayARoleAsSuperAdmin())
        {
            return $next($request);
        }
        else
        {
            throw new LoginFailedException("You Are Not Super Admin !");
        }
    }
}
