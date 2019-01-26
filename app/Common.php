<?php

namespace App;

use App\Enums\QuestionType;

use Illuminate\Support\Facades\Storage;

class Common
{
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
        return rand() % 2;
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
}