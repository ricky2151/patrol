<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;



class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception) && app()->bound('sentry')) {
            app('sentry')->withScope(function (\Sentry\State\Scope $scope) use ($exception): void {
                //set default codeError (if the exception is not listed on app/exceptions)
                $codeError = "E-0000";
                //if exceptions is listed on app/exceptions, then call getCodeException method
                //to get code error. Ex : E-0001 for login failed
                if(method_exists($exception, "getCodeException"))
                {
                    $codeError = $exception->getCodeException();
                }
                //if user is authenticated, then get user id, if not, just fill "null" to userId tag
                $userId = "null";
                if(Auth::user())
                {
                    $userId = Auth::user()->id;
                }
                $scope->setTag('codeError', $codeError);
                $scope->setTag('userId', $userId);
                
                //sent to sentry.io
                app('sentry')->captureException($exception);
            });
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof LoginFailedException){
            return LoginFailedException::render($exception->getMessage()); //E-0001
        }
        else if ($exception instanceof TokenInvalidException) {
            return response()->json(['error' => true,'code' => 'E-0002', 'message' => ["token"=>["Token is Invalid !"]]],400);
        }
        elseif ($exception instanceof TokenExpiredException) {
            return response()->json(['error' => true,'code' => 'E-0002', 'message' => ["token"=>["Token is Expired !"]]],400);
        }
        elseif ($exception instanceof TokenBlacklistedException) {
            return response()->json(['error' => true,'code' => 'E-0002', 'message' => ["token"=>['Token is Blacklist !']]],400);
        }
        else if ($exception instanceof JWTException) {
            return response()->json(['error' => true,'code' => 'E-0002', 'message' => ["Error in Authentication !"]],400);
        }
        else if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => true, 'message'=> "data not found"], 400);
        }
        else if ($exception->getMessage() === 'Token not provided') {
            return response()->json(['error' => true,'code' => 'E-0002', 'message' => ["token"=>['Token not provided !']]], 400);
        }
        elseif ($exception->getMessage() === 'User not found'){
            return response()->json(['error' => true,'code' => 'E-0002', 'message' => ["user"=>['User not found !']]], 400);
        }
        else if($exception instanceof MqttFailedException){
            return MqttFailedException::render($exception->getMessage()); //E-0011
        }
        else if($exception instanceof GetDataFailedException){
            return GetDataFailedException::render($exception->getMessage()); //E-0021
        }
        else if($exception instanceof StoreDataFailedException){
            return StoreDataFailedException::render($exception->getMessage()); //E-0022
        }
        else if($exception instanceof SaveFileFailedException){
            return SaveFileFailedException::render($exception->getMessage()); //E-0023
        }
        else if($exception instanceof SuspiciousInputException){
            return SuspiciousInputException::render($exception->getMessage()); //E-0032
        }
        else if ($exception instanceof ValidationException) 
        { 
            return response()->json(["error" => true,"code" => "E-0031","message" => $exception->errors()], 422);
        }
        else
        {
            return parent::render($request, $exception);
        }
        
    }
}
