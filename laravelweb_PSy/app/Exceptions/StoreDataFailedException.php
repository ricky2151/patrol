<?php

namespace App\Exceptions;

use Exception;

class StoreDataFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0022";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0022",
            "message" => [
                $message
            ]
            ],500);
    }
}