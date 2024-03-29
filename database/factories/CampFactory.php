<?php

use App\Common;
use App\Program;
use App\CampCategory;
use App\CampProcedure;
use App\Organization;
use App\Region;
use App\User;

use App\Enums\EducationLevel;

use Carbon\Carbon;

use Faker\Generator as Faker;

class Camp_Randomizer
{
    protected static $programs, $regions, $education_levels, $organizations, $organizations_top, $now;

    public static function programs()
    {
        if (!self::$programs)
            self::$programs = Program::pluck('id')->toArray();
        return array_rand(array_flip(self::$programs), rand(2, count(self::$programs)));
    }

    public static function education_levels()
    {
        if (!self::$education_levels)
            self::$education_levels = EducationLevel::getConstants();
        return array_rand(array_flip(self::$education_levels), rand(2, count(self::$education_levels)));
    }

    public static function regions()
    {
        if (!self::$regions)
            self::$regions = Region::pluck('id')->toArray();
        return array_rand(array_flip(self::$regions), rand(3, count(self::$regions)));
    }

    public static function organization()
    {
        if (!self::$organizations)
            self::$organizations = User::whereNotNull('organization_id')->groupBy('organization_id')->pluck('organization_id', 'organization_id')->toArray();
        if (!self::$organizations_top) // 115 - 127, 137, 142, 152*, 190*
            self::$organizations_top = Organization::where('image', '!=', '')->pluck('id', 'id')->toArray();
        return array_rand(array_flip(Common::randomVeryFrequentHit() ? self::$organizations_top : self::$organizations));
    }

    public static function date_range_forward($faker, $date, $shift)
    {
        return $faker->dateTimeBetween($date, $date->format('Y-m-d H:i:s').' '.$shift);
    }

    public static function now()
    {
        if (!self::$now)
            self::$now = Carbon::now()->toDateString();
        return self::$now;
    }
}

$factory->define(App\Camp::class, function (Faker $faker) {
    $now = Camp_Randomizer::now();
    $app_open_date = $faker->dateTimeBetween($startDate = $now.' -2 month', $now.' +3 months');
    $app_close_date = Camp_Randomizer::date_range_forward($faker, $app_open_date, '+1 months');
    $camp_procedure = CampProcedure::find(rand(1, CampProcedure::count()));
    $deposit = $camp_procedure->deposit_required ? rand(100, 200) : null;
    $application_fee = $deposit ? null : Common::randomMediumHit() ? rand(100, 500) : null;
    $has_payment = $deposit || $application_fee;
    $announcement_date = $camp_procedure->candidate_required ? Camp_Randomizer::date_range_forward($faker, $app_close_date, '+1 months') : null;
    $interview_date = $camp_procedure->interview_required && $announcement_date ? Camp_Randomizer::date_range_forward($faker, $announcement_date, '+2 weeks') : null;
    $confirmation_date = null;
    $interview_information = null;
    if ($interview_date) {
        $confirmation_date = Camp_Randomizer::date_range_forward($faker, $interview_date, '+2 weeks');
        $interview_information = $faker->sentence($nbWords = 10, $variableNbWords = true);
    } else if ($announcement_date)
        $confirmation_date = Camp_Randomizer::date_range_forward($faker, $announcement_date, '+2 weeks');
    $event_start_date = Camp_Randomizer::date_range_forward($faker, $confirmation_date ? $confirmation_date : $app_close_date, '+1 month');
    $event_end_date = Camp_Randomizer::date_range_forward($faker, $event_start_date, '+2 weeks');
    $camp_name = $faker->unique()->company;
    // TODO: Fake locations
    return [
        'name_en' => "Camp {$camp_name}",
        'name_th' => "ค่าย {$camp_name}",
        'camp_category_id' => rand(1, CampCategory::count()),
        'camp_procedure_id' => $camp_procedure->id,
        'organization_id' => Camp_Randomizer::organization(),
        'acceptable_regions' => Camp_Randomizer::regions(),
        'short_description_en' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'long_description' => $faker->sentence($nbWords = 80, $variableNbWords = true),
        'acceptable_programs' => Camp_Randomizer::programs(),
        'acceptable_education_levels' => Camp_Randomizer::education_levels(),
        'min_cgpa' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1.0, $max = 3.5),
        'other_conditions' => Common::randomMediumHit() ? null : $faker->sentence($nbWords = 10, $variableNbWords = true),
        'url' => Common::randomVeryFrequentHit() ? $faker->unique()->url : null,
        'fburl' => Common::randomMediumHit() ? 'https://facebook.com/'.preg_replace('/\s+/', '-', $faker->unique()->name) : null,
        'app_open_date' => $app_open_date,
        'app_close_date' => $app_close_date,
        'announcement_date' => $announcement_date,
        'interview_date' => $interview_date,
        'confirmation_date' => $confirmation_date,
        'event_start_date' => $event_start_date,
        'event_end_date' => $event_end_date,
        'contact_campmaker' => $faker->address,
        'deposit' => $deposit,
        'application_fee' => $application_fee,
        'payment_information' => $has_payment ? $faker->unique()->bankAccountNumber : null,
        'interview_information' => $interview_information,
        'quota' => Common::randomMediumHit() ? rand(50, 200) : null,
        'backup_limit' => $camp_procedure->candidate_required ? 5 : null,
        'approved' => Common::randomVeryFrequentHit(),
    ];
});
