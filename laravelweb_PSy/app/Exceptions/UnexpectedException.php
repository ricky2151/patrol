<?php

namespace App\Exceptions;

use Exception;

class UnexpectedException extends Exception {

    public static function getCodeException()
    {
        return "E-0041";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0041",
            "message" => [
                $message
            ]
            ],500);
    }
}