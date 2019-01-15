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
        $value = 0;
        $programs = Program::inRandomOrder()->pluck('id'); // TODO: Optimize away
        foreach ($programs as $program) {
            if (rand() % 2)
                $value |= 1 << $program;
        }
        if ($value == 0)
            $value |= 1 << $programs->first();
        return $value;
    }
}

$factory->define(App\Camp::class, function (Faker $faker) {
    return [
        'name_en' => "Camp {$faker->unique()->company}",
        'campcat_id' => CampCategory::inRandomOrder()->first()->id,
        'cp_id' => CampProcedure::inRandomOrder()->first()->id,
        'org_id' => Organization::inRandomOrder()->first()->id,
        'short_description' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'required_programs' => rand() % 2 ? null : Randomizer::programs(),
        'min_gpa' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1.0, $max = 4.0),
        'other_conditions' => rand() % 2 ? null : $faker->sentence($nbWords = 10, $variableNbWords = true),
        'url' => $faker->unique()->url,
    ];
});
