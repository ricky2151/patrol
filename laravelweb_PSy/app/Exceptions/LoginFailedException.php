<?php

namespace App\Exceptions;

use Exception;

class LoginFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0001";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0001",
            "message" => [
                $message
            ]
            ],401);
    }
}