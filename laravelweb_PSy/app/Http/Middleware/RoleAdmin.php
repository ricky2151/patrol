<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Contracts\AuthServiceContract as AuthService;
use App\Exceptions\LoginFailedException;

class RoleAdmin
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
        //harusnya manggil AuthService.checkToken()
        //karena kalau misal ada error, maka tar uda
        //otomatis ke throw errornya

        if($this->authService->canMePlayARoleAsAdmin())
        {
            return $next($request);
        }
        else
        {
            throw new LoginFailedException("You Are Not Admin !");
        }
        
    	

        
        	
        
    }
}
