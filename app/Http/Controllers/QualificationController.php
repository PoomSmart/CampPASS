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
        $pairs = $question_set ? $question_set->pairs()->get() : [];
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id, $graded = true);
        $json['question_scored'] = [];
        foreach ($pairs as $pair) {
            $question = $pair->question();
            $answer = $question_set->answers()->where('camper_id', $camper->id)->where('question_id', $question->id)->get();
            if ($answer->isNotEmpty())
                $answer = $answer->first()->answer;
            else
                $answer = '';
            // Grade the questions that need to be graded and are of choice type (for now)
            if ($question->type == QuestionType::CHOICES && isset($json['question_graded'][$question->json_id])) {
                $solution = $json['radio'][$question->json_id];
                $json['question_scored'][$question->json_id] = $solution == $answer ? $question->full_score : 0;
            }
            $data[] = [
                'question' => $question,
                'answer' => Common::decodeIfNeeded($answer, $question->type),
            ];
        }
        return view('qualification.answer_view', compact('camp', 'camper', 'data', 'json'));
    }
}
