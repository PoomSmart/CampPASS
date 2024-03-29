<?php

namespace App;

use App\Common;
use App\User;
use App\QuestionSet;
use App\QuestionSetQuestionPair;

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
            return json_encode($value, JSON_UNESCAPED_UNICODE);
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
        $json = json_encode($content, JSON_UNESCAPED_UNICODE);
        $directory = self::questionSetDirectory($camp_id);
        Storage::put($directory.'/questions.json', $json);
    }

    public static function createOrUpdateQuestionSet(Camp $camp, $content, $minimum_score, $extra_question_set_info = [], &$question_id = null)
    {
        $question_set = QuestionSet::updateOrCreate([
            'camp_id' => $camp->id,
        ], array_merge(array_merge([
            'minimum_score' => $minimum_score,
        ], $extra_question_set_info)));
        // TODO: How much could we allow for camp makers to make changes on the question set beside the minimum score ?
        if (!$question_set->finalized) {
            if (isset($content['type'])) {
                $questions = $content['type'];
                $question_set_total_score = 0;
                foreach ($questions as $json_id => $type) {
                    $graded = isset($content['question_graded'][$json_id]);
                    $question = Question::updateOrCreate([
                        'json_id' => $json_id,
                    ], array_merge([
                        'type' => (int)$type,
                        'full_score' => $graded ? 10.0 : null,
                    ], is_null($question_id) ? [] : [
                        'id' => ++$question_id,
                    ]));
                    QuestionSetQuestionPair::updateOrCreate([
                        'question_set_id' => $question_set->id,
                        'question_id' => $question->id,
                    ]);
                    if ($graded)
                        $question_set_total_score += $question->full_score;
                }
                $question_set->update([
                    'total_score' => $question_set_total_score,
                ]);
                // We do not need token to be stored
                if (isset($content['_token']))
                    unset($content['_token']);
                $content['camp_id'] = $camp->id;
                self::writeQuestionJSON($camp->id, $content);
            }
        }
        return $question_set;
    }

    public static function getQuestionJSON($camp_id, bool $encode = false, bool $graded = false)
    {
        $json_path = self::questionSetDirectory($camp_id).'/questions.json';
        if ($encode)
            $json = json_encode(Storage::get($json_path), JSON_UNESCAPED_UNICODE);
        else {
            $json = json_decode(Storage::get($json_path), true);
            if (!is_null($graded) && !$graded) {
                // Remove solutions from the questions before responding back to campers
                unset($json['radio']);
                unset($json['checkbox']);
            }
        }
        return $json;
    }

    public static function getAnswers(QuestionSet $question_set, User $camper)
    {
        $answers = $question_set->answers->where('camper_id', $camper->id);
        if ($answers->isEmpty())
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoAnswer'));
        foreach ($answers as $answer) {
            $question = $answer->question;
            $data[] = [
                'question' => $question,
                'answer' => self::decodeIfNeeded($answer->answer, $question->type),
            ];
        }
        return $data;
    }
}