<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Building;
use Faker\Generator as Faker;

$factory->define(Building::class, function (Faker $faker) {
    return [
        'name' => "Gedung " . $faker->name,
    ];
});
