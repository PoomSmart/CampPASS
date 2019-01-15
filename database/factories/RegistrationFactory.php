<?php

use App\Camp;
use App\Camper;
use App\User;

use Faker\Generator as Faker;

$factory->define(App\Registration::class, function (Faker $faker) {
    return [
        'camp_id' => Camp::inRandomOrder()->first()->id,
        'camper_id' => User::_campers(true)->first()->id,
        'approved_by' => User::_campMakers(true)->first()->id,
        'status' => $faker->randomElement($array = array('draft', 'applied', 'returned', 'approved', 'rejected')),
        'submission_time' => $faker->date(),
    ];
});
