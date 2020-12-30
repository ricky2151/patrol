<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Floor;
use Faker\Generator as Faker;


$factory->define(Floor::class, function (Faker $faker) {
    static $number = 1;
    return [
        'name' => "Lantai " . $number++
    ];
});
