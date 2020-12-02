<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;

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
        if ($exception instanceof \Illuminate\Validation\ValidationException) 
        { 
            return response()->json([
                "error" => true,
                "code" => "E-1001",
                "message" => $exception->errors()
            ], 422);
        }
        else if($exception instanceof LoginFailedException){
            return LoginFailedException::render($exception->getMessage());
        }
        else
        {
            return parent::render($request, $exception);
        }
        
    }
}
