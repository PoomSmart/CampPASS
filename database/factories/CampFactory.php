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
        return array_rand(array_flip(self::$programs), rand(2, count(self::$programs)));
    }

    public static function years()
    {
        if (!self::$years)
            self::$years = Year::pluck('id')->toArray();
        return array_rand(array_flip(self::$years), rand(3, count(self::$years)));
    }

    public static function regions()
    {
        if (!self::$regions)
            self::$regions = Region::pluck('id')->toArray();
        return array_rand(array_flip(self::$regions), rand(3, count(self::$regions)));
    }

    public static function date_range_forward($faker, $date, $shift)
    {
        return $faker->dateTimeBetween($date, $date->format('Y-m-d H:i:s').' '.$shift);
    }
}

$factory->define(App\Camp::class, function (Faker $faker) {
    $now = Carbon::now()->format('Y-m-d H:i:s');
    $app_close_date = $faker->dateTimeBetween($startDate = $now.' +10 days', $now.' +6 months');
    $camp_procedure = CampProcedure::find(rand(1, CampProcedure::count()));
    $announcement_date = $camp_procedure->candidate_required ? Camp_Randomizer::date_range_forward($faker, $app_close_date, '+2 months') : null;
    $interview_date = $camp_procedure->interview_required && $announcement_date ? Camp_Randomizer::date_range_forward($faker, $announcement_date, '+2 weeks') : null;
    $confirmation_date = null;
    $interview_information = null;
    if ($interview_date) {
        $confirmation_date = Camp_Randomizer::date_range_forward($faker, $interview_date, '+2 weeks');
        $interview_information = $faker->sentence($nbWords = 10, $variableNbWords = true);
    } else if ($announcement_date)
        $confirmation_date = Camp_Randomizer::date_range_forward($faker, $announcement_date, '+2 weeks');
    $event_start_date = Camp_Randomizer::date_range_forward($faker, $confirmation_date ? $confirmation_date : $app_close_date, '+3 months');
    $event_end_date = Camp_Randomizer::date_range_forward($faker, $event_start_date, '+3 months');
    $camp_name = $faker->unique()->company;
    // TODO: Fake locations
    return [
        'name_en' => "Camp {$camp_name}",
        'name_th' => "ค่าย {$camp_name}",
        'camp_category_id' => rand(1, CampCategory::count()),
        'camp_procedure_id' => $camp_procedure->id,
        'organization_id' => rand(1, Organization::count()),
        'acceptable_regions' => Camp_Randomizer::regions(),
        'short_description_en' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'long_description' => $faker->sentence($nbWords = 80, $variableNbWords = true),
        'acceptable_programs' => Camp_Randomizer::programs(),
        'acceptable_years' => Camp_Randomizer::years(),
        'min_cgpa' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1.0, $max = 3.5),
        'other_conditions' => Common::randomMediumHit() ? null : $faker->sentence($nbWords = 10, $variableNbWords = true),
        'url' => Common::randomVeryFrequentHit() ? $faker->unique()->url : null,
        'fburl' => Common::randomMediumHit() ? 'https://facebook.com/'.$faker->unique()->name : null,
        'app_close_date' => $app_close_date,
        'announcement_date' => $announcement_date,
        'interview_date' => $interview_date,
        'confirmation_date' => $confirmation_date,
        'event_start_date' => $event_start_date,
        'event_end_date' => $event_end_date,
        'interview_information' => $interview_information,
        'quota' => Common::randomMediumHit() ? rand(50, 200) : null,
        'approved' => Common::randomVeryFrequentHit(),
    ];
});
