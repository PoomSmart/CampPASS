<?php

use Faker\Generator as Faker;

$factory->define(App\Organization::class, function (Faker $faker) {
    return [
        'name_en' => $faker->unique()->company,
        'address' => $faker->unique()->address,
        'zipcode' => $faker->postcode,
        'type' => $faker->numberBetween($min = 0, $max = 5), // TODO: discuss
        'subtype' => rand() % 2 ? $faker->numberBetween($min = 0, $max = 3) : null, // TODO: discuss
    ];
});
