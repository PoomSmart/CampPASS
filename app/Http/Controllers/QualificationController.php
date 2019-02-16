<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Common;
use App\FormScore;
use App\QuestionSet;
use App\Registration;

use App\Enums\QuestionType;

use Illuminate\Http\Request;

class QualificationController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Grade an application form from a camper (represented by a registration record) and the respective question set
     * 
     */
    public static function answer_grade($registration_id, $question_set_id, $silent = false)
    {
        $form_score = FormScore::where('registration_id', $registration_id)->where('question_set_id', $question_set_id)->limit(1)->first();
        if ($silent) {
            if ($form_score && isset($form_score->total_score))
                return $form_score->total_score;
        }
        $registration = Registration::findOrFail($registration_id);
        Common::authenticate_camp($registration->camp_id);
        if ($registration->unsubmitted() && !\Auth::user()->isAdmin())
            throw new \CampPASSException('You cannot grade the answers of an unsubmitted form.');
        $camper = $registration->camper();
        $question_set = QuestionSet::findOrFail($question_set_id);
        $answers = Answer::where('question_set_id', $question_set->id)->where('camper_id', $camper->id);
        if (!$answers->exists())
            throw new \CampPASSException('You cannot grade the answers of the application form without questions.');
        $camp = $question_set->camp();
        $answers = $answers->get();
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id, $graded = true);
        if (!$silent) {
            $json['question_scored'] = [];
            $json['question_lock'] = [];
            $json['question_full_score'] = [];
        }
        $auto_gradable_score = 0;
        $total_auto_gradable_score = 0;
        $camper_score = 0;
        $total_score = 0;
        foreach ($answers as $answer) {
            $question = $answer->question();
            $answer_score = $answer->score;
            $answer_value = $answer->answer;
            // Grade the questions that need to be graded and are of choice type
            if (isset($json['question_graded'][$question->json_id])) {
                if ($question->type == QuestionType::CHOICES) {
                    $solution = $json['radio'][$question->json_id];
                    $score = $solution == $answer_value ? $question->full_score : 0;
                    if (!$silent) {
                        $json['question_scored'][$question->json_id] = $score;
                        $json['question_lock'][$question->json_id] = 1;
                    }
                    $auto_gradable_score += $score;
                    $camper_score += $score;
                    if (!isset($answer_score)) {
                        $answer->score = $score;
                        $answer->save();
                    }
                    $total_auto_gradable_score += $question->full_score;
                } else if (isset($answer_score)) {
                    // If the type is not choice, camp makers have graded it and the score has been saved to the database, so we send this information to the view
                    $json['question_scored'][$question->json_id] = $answer_score;
                    $camper_score += $answer_score;
                }
                $total_score += $question->full_score;
            }
            if (!$silent) {
                $json['question_full_score'][$question->json_id] = $question->full_score;
                $data[] = [
                    'question' => $question,
                    'answer' => Common::decodeIfNeeded($answer_value, $question->type),
                ];
            }
        }
        if ($silent) {
            if (!$form_score) {
                FormScore::updateOrcreate([
                    'registration_id' => $registration_id,
                    'question_set_id' => $question_set_id,
                ], [
                    'total_score' => $camper_score,
                    'finalized' => !$question_set->manual_required, // If there are no gradable questions, the form is finalized and can be ranked
                ]);
            }
            return $camper_score;
        } else
            $score_report = "Auto-gradable {$auto_gradable_score}/{$total_auto_gradable_score} - Total {$camper_score}/{$total_score}";
        return view('qualification.answer_grade', compact('camp', 'camper', 'data', 'json', 'score_report', 'form_score'));
    }

    public function save_manual_grade(Request $request, Registration $registration, $question_set_id)
    {
        Common::authenticate_camp($registration->camp_id);
        $form_score = FormScore::where('registration_id', $registration->id)->where('question_set_id', $question_set_id)->limit(1)->first();
        if ($form_score->finalized)
            throw new \CampPASSExceptionRedirectBack('You cannot update the finalized application form.');
        $form_data = $request->all();
        unset($form_data['_token']);
        $camper = $registration->camper();
        $answers = Answer::where('question_set_id', $question_set_id)->where('registration_id', $registration->id)->where('camper_id', $camper->id);
        if (!$answers->exists())
            throw new \CampPASSException('No answers to be saved.');
        $answers = $answers->get();
        foreach ($form_data as $id => $value) {
            if (substr($id, 0, 19) === 'manual_score_range_') {
                $key = substr($id, 19);
                $answer = $answers->filter(function ($answer) use ($key) {
                    return $answer->question()->json_id == $key;
                })->first();
                if (!$answer) {
                    logger()->error('Trying to parse an answer that does not exist.');
                    continue;
                }
                $answer->score = (double)$value;
                $answer->save();
            }
        }
        return redirect()->back()->with('success', 'Scores are updated successfully.');
    }

    public static function form_finalize(FormScore $form_score, $silent = false)
    {
        Common::authenticate_camp($form_score->question_set()->camp()->id, $silent = $silent);
        if (!$form_score->finalized) {
            $form_score->finalized = true;
            $form_score->save();
        }
        return redirect()->back()->with('success', 'This form is finalized.');
    }
}
