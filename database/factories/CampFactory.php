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
            self::$programs = array_column(Program::select('id')->get()->toArray(), 'id', 'id');
        $values = array_rand(self::$programs, rand(1, count(self::$programs)));
        return is_array($values) ? $values : [ $values ];
    }

    public static function years()
    {
        if (!self::$years)
            self::$years = array_column(Year::select('id')->get()->toArray(), 'id', 'id');
        $values = array_rand(self::$years, rand(1, count(self::$years)));
        return is_array($values) ? $values : [ $values ];
    }

    public static function regions()
    {
        if (!self::$regions)
            self::$regions = array_column(Region::select('id')->get()->toArray(), 'id', 'id');
        $values = array_rand(self::$regions, rand(1, count(self::$regions)));
        return is_array($values) ? $values : [ $values ];
    }
}

$factory->define(App\Camp::class, function (Faker $faker) {
    // TODO: biased camp procedure choosing
    $camp_procedure = Common::randomMediumHit() ? CampProcedure::inRandomOrder()->where('candidate_required', true)->first() : CampProcedure::inRandomOrder()->first();
    $event_start_date = $event_end_date = null;
    $now = Carbon::now()->format('Y-m-d H:i:s');
    $app_close_date = $faker->dateTimeBetween($startDate = $now.' +10 days', $now.' +6 months');
    $event_start_date = $faker->dateTimeBetween($app_close_date, $app_close_date->format('Y-m-d H:i:s').' +6 months');
    $event_end_date = $faker->dateTimeBetween($event_start_date, $event_start_date->format('Y-m-d H:i:s').' +3 months');
    // TODO: Fake locations
    return [
        'name_en' => "Camp {$faker->unique()->company}",
        'camp_category_id' => CampCategory::inRandomOrder()->first()->id,
        'camp_procedure_id' => $camp_procedure->id,
        'organization_id' => Organization::inRandomOrder()->first()->id,
        'acceptable_regions' => Camp_Randomizer::regions(),
        'short_description_en' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'long_description' => $faker->sentence($nbWords = 90, $variableNbWords = true),
        'acceptable_programs' => Camp_Randomizer::programs(),
        'acceptable_years' => Camp_Randomizer::years(),
        'min_gpa' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1.0, $max = 3.5),
        'other_conditions' => rand() % 2 ? null : $faker->sentence($nbWords = 10, $variableNbWords = true),
        'url' => $faker->unique()->url,
        'app_close_date' => $app_close_date,
        'event_start_date' => $event_start_date,
        'event_end_date' => $event_end_date,
        'approved' => Common::randomFrequentHit() ? true : false,
    ];
});
