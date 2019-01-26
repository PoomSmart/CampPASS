<?php

use App\Common;
use App\Program;
use App\CampCategory;
use App\CampProcedure;
use App\Organization;
use App\Region;

use Faker\Generator as Faker;

class Randomizer
{
    protected static $programs, $regions;

    public static function programs()
    {
        if (!self::$programs)
            self::$programs = Program::pluck('id')->toArray();
        shuffle(self::$programs);
        $values = array_rand(self::$programs, rand(1, count(self::$programs)));
        return is_array($values) ? $values : [ $values ];
    }

    public static function regions()
    {
        if (!self::$regions)
            self::$regions = Region::pluck('id')->toArray();
        shuffle(self::$regions);
        $values = array_rand(self::$regions, rand(1, count(self::$regions)));
        return is_array($values) ? $values : [ $values ];
    }
}

$factory->define(App\Camp::class, function (Faker $faker) {
    $camp_procedure = CampProcedure::inRandomOrder()->first();
    $app_open_date = $camp_procedure->candidate_required ? Common::randomVeryFrequentHit() ? $faker->dateTimeBetween($startDate = '-6 months', '+6 months') : null : null;
    $app_close_date = $app_open_date && $camp_procedure->candidate_required && Common::randomVeryFrequentHit() ? $faker->dateTimeBetween($app_open_date, $app_open_date->format('Y-m-d H:i:s').' +6 months') : null;
    $reg_open_date = !$camp_procedure->candidate_required ? Common::randomVeryFrequentHit() ? $faker->dateTimeBetween($startDate = '-6 months', '+6 months') : null : null;
    $reg_close_date = $reg_open_date && !$camp_procedure->candidate_required && Common::randomVeryFrequentHit() ? $faker->dateTimeBetween($reg_open_date, $reg_open_date->format('Y-m-d H:i:s').' +6 months') : null;
    $event_start_date = $event_end_date = null;
    if ($app_close_date)
        $event_start_date = $faker->dateTimeBetween($app_close_date, $app_close_date->format('Y-m-d H:i:s').' +6 months');
    else if ($reg_close_date)
        $event_start_date = $faker->dateTimeBetween($reg_close_date, $reg_close_date->format('Y-m-d H:i:s').' +6 months');
    if ($event_start_date)
        $event_end_date = $faker->dateTimeBetween($event_start_date, $event_start_date->format('Y-m-d H:i:s').' +6 months');
    // TODO: Fake locations
    return [
        'name_en' => "Camp {$faker->unique()->company}",
        'camp_category_id' => CampCategory::inRandomOrder()->first()->id,
        'camp_procedure_id' => $camp_procedure->id,
        'organization_id' => Organization::inRandomOrder()->first()->id,
        'acceptable_regions' => Randomizer::regions(),
        'short_description_en' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'acceptable_programs' => Randomizer::programs(),
        'min_gpa' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1.0, $max = 3.5),
        'other_conditions' => rand() % 2 ? null : $faker->sentence($nbWords = 10, $variableNbWords = true),
        'url' => $faker->unique()->url,
        'app_open_date' => $app_open_date,
        'app_close_date' => $app_close_date,
        'reg_open_date' => $reg_open_date,
        'reg_close_date' => $reg_close_date,
        'event_start_date' => $event_start_date,
        'event_end_date' => $event_end_date,
        'approved' => Common::randomFrequentHit() ? true : false,
    ];
});
