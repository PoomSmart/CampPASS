<?php

namespace App;

use App\Enums\QuestionType;

use Illuminate\Support\Facades\Storage;

class Common
{
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
}