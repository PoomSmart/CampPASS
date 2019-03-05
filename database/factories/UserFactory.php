<?php

use App\Common;
Use App\Religion;
use App\School;
use App\Program;
use App\Province;
use App\Organization;

use App\Enums\Gender;
use App\Enums\EducationLevel;

use Faker\Generator as Faker;

class User_Randomizer
{
    protected static $CAMPER = null;
    protected static $CAMPMAKER = null;

    /**
     * Randomize Thai citizen ID (Only for testing purpose).
     * http://kiss-hack.blogspot.com/2013/09/random-number-13.html
     * 
     */
    public static function citizenID() {
        $firstNumber = rand(1, 8);
        $numberCalc = 13 * $firstNumber;
        for ($i = 12; $i > 1; $i--) {
            $m = rand(0, 9);
            $firstNumber .= $m;
            $numberCalc += ($i * $m);
        }
        $lastNumber = (11 - ($numberCalc % 11)) % 10;
        return $firstNumber.$lastNumber;
    }

    public static function camper()
    {
        if (!self::$CAMPER)
            self::$CAMPER = config('const.account.camper');
        return self::$CAMPER;
    }

    public static function campmaker()
    {
        if (!self::$CAMPMAKER)
            self::$CAMPMAKER = config('const.account.campmaker');
        return self::$CAMPMAKER;
    }
}

$factory->define(App\User::class, function (Faker $faker) {
    $name = $faker->unique()->firstName;
    $surname = $faker->lastName;
    $type = Common::randomFrequentHit() ? User_Randomizer::camper() : User_Randomizer::campmaker();
    $dob = $type == User_Randomizer::camper() ? $faker->dateTimeBetween($startDate = '-19 years', '-10 years') : $faker->dateTimeBetween($startDate = '-40 years', '-19 years');
    $province = Province::inRandomOrder()->get()->first();
    $data = [
        'username' => strtolower($name),
        'name_en' => $name,
        'surname_en' => $surname,
        'nickname_en' => $faker->word,
        'nationality' => rand(0, 1),
        'gender' => Common::randomMediumHit() ? Gender::FEMALE : Gender::any(),
        'citizen_id' => User_Randomizer::citizenID(),
        'dob' => $dob,
        'street_address' => $faker->address,
        'province_id' => $province->id,
        'zipcode' => $province->zipcode_prefix.implode('', $faker->randomElements($array = range(0, 9), $count = 3, $allowDuplicates = true)),
        'mobile_no' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        'religion_id' => rand(1, Religion::count()),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'type' => $type,
        'password' => '123456',
        'remember_token' => str_random(10),
    ];
    if ($type == User_Randomizer::camper()) {
        $data += [
            'education_level' => EducationLevel::any(),
            'blood_group' => rand(0, 3),
            'cgpa' => rand(200, 400) / 100.0, // Assume campers are not that incompetent
            'school_id' => rand(1, School::count()),
            'program_id' => rand(1, Program::count()),
            'guardian_name' => $faker->firstName,
            'guardian_surname' => $surname,
            'guardian_role' => Common::randomMediumHit(),
            'guardian_mobile_no' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        ];
    } else {
        $data += [
            'organization_id' => rand(1, Organization::count()),
        ];
    }
    return $data;
});
