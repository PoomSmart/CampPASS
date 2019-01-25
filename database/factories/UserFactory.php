<?php

Use App\Religion;

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    $name = $faker->unique()->firstName;
    $type = rand() % 2 ? config('const.account.camper') : config('const.account.campmaker');
    $dob = $type == config('const.account.camper') ? $faker->dateTimeBetween($startDate = '-19 years', '-10 years') : $faker->dateTimeBetween($startDate = '-40 years', '-19 years');
    return [
        'username' => strtolower($name),
        'name_en' => $name,
        'surname_en' => $faker->lastName,
        'nickname_en' => $faker->word,
        'nationality' => $faker->numberBetween($min = 0, $max = 1),
        'gender' => $faker->numberBetween($min = 0, $max = 2),
        'citizen_id' => implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 13, $allowDuplicates = true)),
        'dob' => $dob,
        'address' => $faker->address,
        'zipcode' => $faker->postcode,
        'mobile_no' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        'religion_id' => Religion::inRandomOrder()->first()->id,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'type' => $type,
        'password' => bcrypt('123456'), // secret
        'remember_token' => str_random(10),
    ];
});
