<?php

namespace App\Exceptions;

use Exception;

class UpdateDataFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0024";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0024",
            "message" => [
                $message
            ]
        ],500);
    }
}