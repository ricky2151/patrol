<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Room;
use Faker\Generator as Faker;

$factory->define(Room::class, function (Faker $faker) {
    return [
        "name" => "Ruangan " . $faker->name,
        "floor_id" => mt_rand(1,5),
        "building_id" => mt_rand(1,5),
        "gateway_id" => mt_rand(1,5)
    ];
});
