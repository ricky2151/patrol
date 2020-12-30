<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\History;
use Faker\Generator as Faker;
use App\Models\Shift;

$factory->define(History::class, function (Faker $faker) {
    //shift with specific id have a time data. get start time data.
    //use start time data to fill scan_time.
    $shiftId = mt_rand(1,2);
    echo "cek shiftIdnya : " . $shiftId . "\n";
    echo Shift::find($shiftId)->get()[0]->time()->get();
    echo "\n===\n";
    $scanTime = Shift::find($shiftId)->get()[0]->time()->get()[0]->start;
    return [
        "shift_id" => $shiftId,
        "status_node_id" => mt_rand(1,2),
        "message" => $faker->randomElement(['Aman pak !', 'Waduh Mencurigakan !', 'Ada malling !!!', 'Sepiii']),
        "scan_time" => $scanTime . ":05"
    ];
});
