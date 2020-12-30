<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Time;
use Faker\Generator as Faker;

$factory->define(Time::class, function (Faker $faker) {
    return [
        "start" => "00:00",
        "end" => "06:00"
    ];
});
