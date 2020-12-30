<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'age' => mt_rand(30,60),
        'role_id' => 1, //guard
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
        'phone' => $faker->PhoneNumber,
        'master_key' => Str::random(16)
    ];
});
