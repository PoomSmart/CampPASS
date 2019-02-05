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
    function __construct()
    {
        $this->middleware('permission:answer-grade', ['only' => 'answer_grade', 'save_manual_grade']);
    }

    public function answer_grade($registration_id, $question_set_id)
    {
        $registration = Registration::findOrFail($registration_id);
        $camper = $registration->camper();
        if ($registration->unsubmitted())
            throw new \App\Exceptions\CampPassException('You cannot grade the answers of an unsubmitted form.');
        $question_set = QuestionSet::findOrFail($question_set_id);
        $camp = $question_set->camp();
        $answers = Answer::where('question_set_id', $question_set->id)->where('camper_id', $camper->id);
        if (!$answers->exists())
            throw new \App\Exceptions\CampPassException('You cannot grade the answers of the application form without questions.');
        $answers = $answers->get();
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id, $graded = true);
        $json['question_scored'] = [];
        $json['question_lock'] = [];
        $json['question_full_score'] = [];
        $auto_gradable_score = 0;
        $total_auto_gradable_score = 0;
        $camper_score = 0;
        $total_score = 0;
        foreach ($answers as $answer) {
            $question = $answer->question();
            $answer_score = $answer->score;
            $answer = $answer->answer;
            // Grade the questions that need to be graded and are of choice type (for now)
            if (isset($json['question_graded'][$question->json_id])) {
                if ($question->type == QuestionType::CHOICES) {
                    $solution = $json['radio'][$question->json_id];
                    $score = $solution == $answer ? $question->full_score : 0;
                    $json['question_scored'][$question->json_id] = $score;
                    $json['question_lock'][$question->json_id] = 1;
                    $auto_gradable_score += $score;
                    $camper_score += $score;
                    $total_auto_gradable_score += $question->full_score;
                } else if (isset($answer_score)) {
                    $json['question_scored'][$question->json_id] = $answer_score;
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
        $registration_id = $registration->id;
        $question_set_id = $question_set->id;
        return view('qualification.answer_grade', compact('camp', 'camper', 'data', 'json', 'score_report', 'registration_id', 'question_set_id'));
    }

    public function save_manual_grade(Request $request, Registration $registration, $question_set_id)
    {
        $form_data = $request->all();
        unset($form_data['_token']);
        $camper = $registration->camper();
        $answers = Answer::where('question_set_id', $question_set_id)->where('registration_id', $registration->id)->where('camper_id', $camper->id);
        if (!$answers->exists())
            throw new \App\Exceptions\CampPASSException('No answers to be saved.');
        $answers = $answers->get();
        foreach ($form_data as $id => $value) {
            if (substr($id, 0, 19) === 'manual_score_range_') {
                $key = substr($id, 19);
                $answer = $answers->filter(function ($answer) use ($key) {
                    return $answer->question()->json_id == $key;
                })->first();
                $answer->score = (double)$value;
                $answer->save();
            }
        }
        return redirect()->back()->with('success', 'Scores are updated successfully.');
    }
}
