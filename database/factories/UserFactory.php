<?php

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

$factory->define(App\User::class, function (Faker $faker) {
    $name = $faker->unique()->firstName;
    return [
        'username' => strtolower($name),
        'name_en' => $name,
        'surname_en' => $faker->lastName,
        'nickname_en' => $faker->word,
        'nationality' => $faker->numberBetween($min = 0, $max = 1),
        'gender' => $faker->numberBetween($min = 0, $max = 2),
        'citizen_id' => implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 13, $allowDuplicates = true)),
        'dob' => $faker->date($format = 'Y-m-d'),
        'address' => $faker->address,
        'zipcode' => $faker->postcode,
        'mobile_no' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'type' => rand() % 2 ? config('const.account.camper') : config('const.account.campmaker'),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});
