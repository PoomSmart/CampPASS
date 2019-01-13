<?php

use Faker\Generator as Faker;

$factory->define(App\School::class, function (Faker $faker) {
    return [
        'name_en' => $faker->unique()->company,
        'address' => $faker->unique()->address,
        'zipcode' => $faker->postcode,
        'type' => $faker->numberBetween($min = 0, $max = 5), // TODO: discuss
    ];
});
