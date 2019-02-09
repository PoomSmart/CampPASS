<?php

namespace App;

use App\Enums\QuestionType;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Common
{
    public static $north_region = [
        50, 51, 52, 53, 54, 55, 56, 57, 58,
    ];

    public static $northeast_region = [
        30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
    ];

    public static $central_region = [
        10, 11, 12, 13, 14, 15, 16, 17, 18, 26, 60, 61, 62, 64, 65, 66, 67, 72, 73, 74, 75,
    ];

    public static $east_region = [
        20, 21, 22, 23, 24, 25, 27,
    ];

    public static $west_region = [
        63, 70, 71, 76, 77,
    ];

    public static $south_region = [
        80, 81, 82, 83, 84, 85, 86, 90, 91, 92, 93, 94, 95, 96,
    ];

    public static function randomFrequentHit()
    {
        return rand(0, 10) > 3;
    }

    public static function randomVeryFrequentHit()
    {
        return rand(0, 10) > 1;
    }

    public static function randomMediumHit()
    {
        return rand() & 1;
    }

    public static function randomRareHit()
    {
        return rand(0, 10) > 6;
    }

    public static function randomVeryRareHit()
    {
        return rand(0, 10) > 8;
    }

    public static function campDirectory($camp_id)
    {
        return "camps/{$camp_id}";
    }

    public static function registrationDirectory($camp_id)
    {
        return self::campDirectory($camp_id)."/registrations";
    }

    public static function questionSetDirectory($camp_id)
    {
        return self::campDirectory($camp_id)."/questions";
    }

    public static function encodeIfNeeded($value, $question_type)
    {
        if ($question_type == QuestionType::CHECKBOXES)
            return json_encode($value);
        return $value;
    }

    public static function decodeIfNeeded($value, $question_type)
    {
        if ($question_type == QuestionType::CHECKBOXES)
            return json_decode($value);
        return $value;
    }

    public static function getQuestionJSON($camp_id, $graded = false)
    {
        $json_path = self::questionSetDirectory($camp_id).'/questions.json';
        $json = json_decode(Storage::disk('local')->get($json_path), true);
        if (!is_null($graded) && !$graded) {
            // Remove solutions from the questions before responding back to campers
            unset($json['radio']);
            unset($json['checkbox']);
        }
        return $json;
    }

    public static function randomString($length = 6)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function getLocalizedName($record, $attribute = 'name')
    {
        $th = $record->{"{$attribute}_th"};
        $en = $record->{"{$attribute}_en"};
        if ((\App::getLocale() == 'th' && !is_null($th)) || is_null($en))
            return $th;
        return $en ? $en : '<blank>';
    }

    public static function values($clazz, $column = null, $value = null)
    {
        if ($column && $value)
            $values = $clazz::where($column, $value)->get();
        else
            $values = $clazz::all();
        return Arr::sort($values, function($record) {
            return $record->__toString();
        });
    }
}