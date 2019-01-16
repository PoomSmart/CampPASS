<?php

use App\Program;
use App\CampCategory;
use App\CampProcedure;
use App\Organization;

use Faker\Generator as Faker;

class Randomizer
{
    public static function programs()
    {
        $programs = Program::inRandomOrder();
        $programs = $programs->limit(rand(1, $programs->count()))->pluck('id'); // TODO: Optimize away
        return $programs->toArray();
    }
}

$factory->define(App\Camp::class, function (Faker $faker) {
    return [
        'name_en' => "Camp {$faker->unique()->company}",
        'campcat_id' => CampCategory::inRandomOrder()->first()->id,
        'cp_id' => CampProcedure::inRandomOrder()->first()->id,
        'org_id' => Organization::inRandomOrder()->first()->id,
        'short_description_en' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'required_programs' => rand() % 2 ? null : Randomizer::programs(),
        'min_gpa' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1.0, $max = 4.0),
        'other_conditions' => rand() % 2 ? null : $faker->sentence($nbWords = 10, $variableNbWords = true),
        'url' => $faker->unique()->url,
    ];
});
