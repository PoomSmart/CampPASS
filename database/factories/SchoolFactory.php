<?php

use App\Enums\SchoolType;

use Faker\Generator as Faker;

$factory->define(App\School::class, function (Faker $faker) {
    return [
        'name_en' => $faker->unique()->company,
        'address' => $faker->unique()->address,
        'zipcode' => $faker->postcode,
        'type' => SchoolType::any(),
    ];
});
