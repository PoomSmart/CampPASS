<?php

use App\Common;
Use App\Religion;

use App\Enums\Gender;

use Faker\Generator as Faker;

class User_Randomizer
{
    public static function zipcode_prefix()
    {
        $region = rand(0, 5);
        $region_array = null;
        switch ($region) {
            case 0:
                $region_array = Common::$north_region;
                break;
            case 1:
                $region_array = Common::$northeast_region;
                break;
            case 2:
                $region_array = Common::$central_region;
                break;
            case 3:
                $region_array = Common::$east_region;
                break;
            case 4:
                $region_array = Common::$west_region;
                break;
            case 5:
                $region_array = Common::$south_region;
                break;
        }
        return $region_array[array_rand($region_array)];
    }

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
}

$factory->define(App\User::class, function (Faker $faker) {
    $name = $faker->unique()->firstName;
    $type = Common::randomFrequentHit() ? config('const.account.camper') : config('const.account.campmaker');
    $dob = $type == config('const.account.camper') ? $faker->dateTimeBetween($startDate = '-19 years', '-10 years') : $faker->dateTimeBetween($startDate = '-40 years', '-19 years');
    return [
        'username' => strtolower($name),
        'name_en' => $name,
        'surname_en' => $faker->lastName,
        'nickname_en' => $faker->word,
        'nationality' => $faker->numberBetween($min = 0, $max = 1),
        'gender' => Gender::any(),
        'citizen_id' => User_Randomizer::citizenID(),
        'dob' => $dob,
        'address' => $faker->address,
        'zipcode' => User_Randomizer::zipcode_prefix().implode('', $faker->randomElements($array = range(0, 9), $count = 3, $allowDuplicates = true)),
        'mobile_no' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        'religion_id' => Religion::inRandomOrder()->first()->id,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'type' => $type,
        'password' => bcrypt('123456'),
        'remember_token' => str_random(10),
    ];
});
