<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Common;
use App\QuestionSet;
use App\Registration;

use App\Enums\QuestionType;

use Illuminate\Http\Request;

class QualificationController extends Controller
{
    public function answer_view($registration_id, $question_set_id)
    {
        $registration = Registration::findOrFail($registration_id);
        $camper = $registration->camper();
        if (!$camper->isCamper())
            throw new \App\Exceptions\CampPassException(trans('app.InternalError'));
        if ($registration->unsubmitted())
            throw new \App\Exceptions\CampPassException('You cannot view the answers of an unsubmitted form.');
        $question_set = QuestionSet::findOrFail($question_set_id);
        $camp = $question_set->camp();
        $answers = Answer::where('question_set_id', $question_set->id)->get()->where('camper_id', $camper->id);
        if (empty($answers))
            throw new \App\Exceptions\CampPassException('You cannot view the answers of the application form without questions.');
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id, $graded = true);
        $json['question_scored'] = [];
        $json['question_full_score'] = [];
        $auto_gradable_score = 0;
        $total_auto_gradable_score = 0;
        $camper_score = 0;
        $total_score = 0;
        foreach ($answers as $answer) {
            $question = $answer->question();
            $answer = $answer->answer;
            // Grade the questions that need to be graded and are of choice type (for now)
            if (isset($json['question_graded'][$question->json_id])) {
                if ($question->type == QuestionType::CHOICES) {
                    $solution = $json['radio'][$question->json_id];
                    $score = $solution == $answer ? $question->full_score : 0;
                    $json['question_scored'][$question->json_id] = $score;
                    $auto_gradable_score += $score;
                    $camper_score += $score;
                    $total_auto_gradable_score += $question->full_score;
                }
                $total_score += $question->full_score;
            }
            $json['question_full_score'][$question->json_id] = $question->full_score;
            $data[] = [
                'question' => $question,
                'answer' => Common::decodeIfNeeded($answer, $question->type),
            ];
        }
        $score_report = "Auto-gradable {$auto_gradable_score}/{$total_auto_gradable_score} - Total {$camper_score}/{$total_score}";
        return view('qualification.answer_view', compact('camp', 'camper', 'data', 'json', 'score_report'));
    }
}
