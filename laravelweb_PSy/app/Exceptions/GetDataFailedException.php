<?php

namespace App\Exceptions;

use Exception;

class GetDataFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0021";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0021",
            "message" => [
                $message
            ]
            ],500);
    }
}