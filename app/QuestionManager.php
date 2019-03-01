<?php

namespace App;

use App\Common;

use App\Enums\QuestionType;

use Illuminate\Support\Facades\Storage;

class QuestionManager
{
    public static function questionSetDirectory(int $camp_id)
    {
        return Common::campDirectory($camp_id)."/questions";
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

    public static function writeQuestionJSON($camp_id, $content)
    {
        $json = json_encode($content);
        $directory = self::questionSetDirectory($camp_id);
        Storage::disk('local')->put($directory.'/questions.json', $json);
    }

    public static function getQuestionJSON($camp_id, bool $encode = false, bool $graded = false)
    {
        $json_path = self::questionSetDirectory($camp_id).'/questions.json';
        if ($encode)
            $json = json_encode(Storage::disk('local')->get($json_path));
        else {
            $json = json_decode(Storage::disk('local')->get($json_path), true);
            if (!is_null($graded) && !$graded) {
                // Remove solutions from the questions before responding back to campers
                unset($json['radio']);
                unset($json['checkbox']);
            }
        }
        return $json;
    }
}