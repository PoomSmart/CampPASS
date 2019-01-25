<?php

namespace App\Http\Controllers;

use App\Common;
use App\QuestionSet;
use App\Registration;

use App\Enums\RegistrationStatus;
use App\Enums\QuestionType;

use Illuminate\Http\Request;

class QualificationController extends Controller
{
    public function answer_view($registration_id, $question_set_id)
    {
        $registration = Registration::findOrFail($registration_id);
        $camper = $registration->camper();
        if (!$camper->isCamper())
            return redirect('/')->with('error', 'app.InternalError');
        if ($registration->status == RegistrationStatus::DRAFT || $registration->status == RegistrationStatus::RETURNED)
            return redirect()->back()->with('error', 'You cannot view the answers of an unsubmitted form.');
        $question_set = QuestionSet::findOrFail($question_set_id);
        $camp = $question_set->camp();
        if ($question_set->pairs())
            $pairs = $question_set->pairs()->get();
        else
            return redirect('/')->with('error', 'You cannot view the answers of the application form without questions.');
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id, $graded = true);
        $json['question_scored'] = [];
        $auto_gradable_score = 0;
        $total_auto_gradable_score = 0;
        $camper_score = 0;
        $total_score = 0;
        foreach ($pairs as $pair) {
            $question = $pair->question();
            $answer = $question_set->answers()->where('camper_id', $camper->id)->where('question_id', $question->id)->get();
            // An answer may be null if the camper did not fill it in
            if ($answer->isNotEmpty())
                $answer = $answer->first()->answer;
            else
                $answer = '';
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
            $data[] = [
                'question' => $question,
                'answer' => Common::decodeIfNeeded($answer, $question->type),
            ];
        }
        $score_report = "Auto-gradable {$auto_gradable_score}/{$total_auto_gradable_score} - Total {$camper_score}/{$total_score}";
        return view('qualification.answer_view', compact('camp', 'camper', 'data', 'json', 'score_report'));
    }
}
