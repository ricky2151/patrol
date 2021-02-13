<?php

namespace App\Exceptions;

use Exception;

class DeleteDataFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0025";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0025",
            "message" => [
                $message
            ]
        ],500);
    }
}