<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\StatusNode;
use Faker\Generator as Faker;

$factory->define(StatusNode::class, function (Faker $faker) {
    return [
        "name" => "Aman"
    ];
});
