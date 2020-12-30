<?php

namespace App\Exceptions;

use Exception;

class MqttFailedException extends Exception {

    public static function getCodeException()
    {
        return "E-0011";
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
            ],503);
    }
}