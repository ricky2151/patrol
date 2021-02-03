<?php

namespace App\Exceptions;

use Exception;

class SaveFileFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0023";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0023",
            "message" => [
                $message
            ]
        ],500);
    }
}