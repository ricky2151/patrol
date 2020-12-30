<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Gateway;
use Faker\Generator as Faker;

$factory->define(Gateway::class, function (Faker $faker) {
    static $number = 1;
    return [
        'name' => "GT-" . $number++,
        "location" => "Pojokan Ruang " . $faker->name,
    ];
});
