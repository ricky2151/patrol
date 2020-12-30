<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Shift;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Shift::class, function (Faker $faker) {
    return [
        "user_id" => 1,
        'room_id' => 1,
        'time_id' => 1,
        'date' => Carbon::today()->format('Y-m-d')
    ];
});
