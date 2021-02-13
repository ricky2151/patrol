<?php

namespace App\Exceptions;

use Exception;

class LogoutFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0003";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0003",
            "message" => [
                $message
            ]
        ],401);
    }
}