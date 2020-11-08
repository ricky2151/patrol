<?php
/**
 * Created by PhpStorm.
 * User: salman
 * Date: 2/22/19
 * Time: 1:29 PM
 */

return [

    'host' => env('mqtt_host','broker.shiftr.io'),
    'password' => env('mqtt_password','cfdc8ca761caadf9'),
    'username' => env('mqtt_username','904e4807'),
    'certfile' => env('mqtt_cert_file',''),
    'port' => env('mqtt_port','1883'),
    'debug' => env('mqtt_debug',false) //Optional Parameter to enable debugging set it to True
];
