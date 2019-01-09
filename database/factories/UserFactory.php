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
        'nameen' => $name,
        'surnameen' => $faker->lastName,
        'nicknameen' => $faker->word,
        'nationality' => $faker->numberBetween($min = 0, $max = 1),
        'gender' => $faker->numberBetween($min = 0, $max = 2),
        'citizenid' => implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 13, $allowDuplicates = true)),
        'dob' => $faker->date($format = 'Y-m-d'),
        'address' => $faker->address,
        'zipcode' => $faker->postcode,
        'mobileno' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'type' => $faker->numberBetween($min = 1, $max = 2),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});
