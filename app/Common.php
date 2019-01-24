<?php

namespace App;

use App\Enums\QuestionType;

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
}