<?php

use Faker\Generator as Faker;

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
        'rel_id' => DB::table('religions')->inRandomOrder()->pluck('id')->first(),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'type' => rand() % 2 ? config('const.account.camper') : config('const.account.campmaker'),
        'password' => '123456', // secret
        'remember_token' => str_random(10),
    ];
});
