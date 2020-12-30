<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Photo;
use Faker\Generator as Faker;
use App\Models\History;

$factory->define(Photo::class, function (Faker $faker) {
    //history with id 1 have a shift. Shift have a time data. get start time data.
    //use start time data to fill photo_time.
    $photoTime = History::find(1)->get()[0]->shift()->get()[0]->time()->get()[0]->start;
    
    return [
        "url" => "www.google.com",
        "history_id" => 1,
        "photo_time" => $photoTime . ":05"
    ];
});
