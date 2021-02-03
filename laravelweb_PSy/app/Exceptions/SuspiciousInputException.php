<?php

namespace App\Exceptions;

use Exception;

class SuspiciousInputException extends Exception {

    public static function getCodeException()
    {
        return "E-0032";
    }

    public static function render($message) {
        if($message == "")
        {
            $message = "undefined error";
        }
        return response()->json([
            "error" => true,
            "code" => "E-0032",
            "message" => $message
        ],400);
    }
}