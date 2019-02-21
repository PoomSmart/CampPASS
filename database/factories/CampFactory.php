<?php

use App\Common;
use App\Program;
use App\CampCategory;
use App\CampProcedure;
use App\Organization;
use App\Region;
use App\Year;

use Carbon\Carbon;

use Faker\Generator as Faker;

class Camp_Randomizer
{
    protected static $programs, $regions, $years;

    public static function programs()
    {
        if (!self::$programs)
            self::$programs = Program::pluck('id')->toArray();
        $values = array_rand(self::$programs, rand(2, count(self::$programs)));
        return is_array($values) ? $values : [ $values ];
    }

    public static function years()
    {
        if (!self::$years)
            self::$years = Year::pluck('id')->toArray();
        $values = array_rand(self::$years, rand(3, count(self::$years)));
        return is_array($values) ? $values : [ $values ];
    }

    public static function regions()
    {
        if (!self::$regions)
            self::$regions = Region::pluck('id')->toArray();
        $values = array_rand(self::$regions, rand(3, count(self::$regions)));
        return is_array($values) ? $values : [ $values ];
    }
}

$factory->define(App\Camp::class, function (Faker $faker) {
    $now = Carbon::now()->format('Y-m-d H:i:s');
    $app_close_date = $faker->dateTimeBetween($startDate = $now.' +10 days', $now.' +6 months');
    $event_start_date = $faker->dateTimeBetween($app_close_date, $app_close_date->format('Y-m-d H:i:s').' +6 months');
    $event_end_date = $faker->dateTimeBetween($event_start_date, $event_start_date->format('Y-m-d H:i:s').' +3 months');
    // TODO: Fake locations
    return [
        'name_en' => "Camp {$faker->unique()->company}",
        'camp_category_id' => rand(1, CampCategory::count()),
        'camp_procedure_id' => rand(1, CampProcedure::count()),
        'organization_id' => rand(1, Organization::count()),
        'acceptable_regions' => Camp_Randomizer::regions(),
        'short_description_en' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'long_description' => $faker->sentence($nbWords = 90, $variableNbWords = true),
        'acceptable_programs' => Camp_Randomizer::programs(),
        'acceptable_years' => Camp_Randomizer::years(),
        'min_cgpa' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1.0, $max = 3.5),
        'other_conditions' => Common::randomMediumHit() ? null : $faker->sentence($nbWords = 10, $variableNbWords = true),
        'url' => $faker->unique()->url,
        'app_close_date' => $app_close_date,
        'event_start_date' => $event_start_date,
        'event_end_date' => $event_end_date,
        'quota' => Common::randomMediumHit() ? rand(50, 200) : null,
        'approved' => Common::randomVeryFrequentHit(),
    ];
});
