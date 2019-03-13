<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Camp;
use App\Common;
use App\FormScore;
use App\QuestionSet;
use App\Registration;
use App\QuestionManager;

use App\Enums\QuestionType;

use Illuminate\Http\Request;

class QualificationController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:camper-list');
        $this->middleware('permission:answer-grade', ['only' => ['form_grade', 'save_manual_grade', 'form_finalize']]);
        $this->middleware('permission:candidate-list', ['only' => ['candidate_rank', 'candidate_announce']]);
        $this->middleware('permission:candidate-edit', ['only' => ['form_check']]);
    }

    /**
     * Grade an application form from a camper (represented by a registration record) and the respective question set
     * 
     */
    public static function form_grade($registration_id, $question_set_id, bool $silent = false)
    {
        $form_score = FormScore::where('registration_id', $registration_id)->where('question_set_id', $question_set_id)->limit(1)->first();
        if ($silent) {
            if ($form_score && isset($form_score->total_score))
                return $form_score->total_score;
        }
        $registration = Registration::findOrFail($registration_id);
        Common::authenticate_camp($registration->camp);
        if ($registration->unsubmitted())
            throw new \CampPASSException(trans('exception.CannotGradeUnsubmittedForm'));
        $camper = $registration->camper;
        $question_set = QuestionSet::findOrFail($question_set_id);
        $answers = Answer::where('question_set_id', $question_set_id)->where('registration_id', $registration_id);
        if ($answers->doesntExist()) // This should not happen
            throw new \CampPASSException(trans('exception.CannotGradeFormWithoutQuestions'));
        $answers = $answers->get();
        $data = [];
        $json = QuestionManager::getQuestionJSON($question_set->camp_id, $encode = false, $graded = true);
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
            $question = $answer->question;
            if ($form_score && $form_score->finalized)
                $json['question_lock'][$question->json_id] = 1;
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
                        $answer->update([
                            'score' => $score,
                        ]);
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
                    'answer' => QuestionManager::decodeIfNeeded($answer_value, $question->type),
                ];
            }
        }
        if (!$form_score) {
            $form_score = FormScore::updateOrCreate([
                'registration_id' => $registration_id,
                'question_set_id' => $question_set_id,
            ], [
                'total_score' => $camper_score,
                'finalized' => !$question_set->manual_required, // If there are no gradable questions, the form is finalized and can be ranked
                'submission_time' => $registration->submission_time,
            ]);
        }
        if ($silent)
            return $camper_score;
        if ($total_score) {
            if ($question_set->manual_required)
                $score_report = trans('qualification.FormSummary', [
                    'camper_score' => $camper_score,
                    'total_score' => $total_score,
                ]);
            else
                $score_report = $total_auto_gradable_score ? trans('qualification.FormSummaryAuto', [
                    'auto_gradable' => $auto_gradable_score,
                    'total_auto_gradable' => $total_auto_gradable_score,
                    'camper_score' => $camper_score,
                    'total_score' => $total_score,
                ]) : null;
        } else
            $score_report = null;
        return view('qualification.form_grade', compact('camper', 'data', 'json', 'score_report', 'form_score'));
    }

    public function save_manual_grade(Request $request, Registration $registration, $question_set_id)
    {
        Common::authenticate_camp($registration->camp);
        $form_score = FormScore::where('registration_id', $registration->id)->where('question_set_id', $question_set_id)->limit(1)->first();
        if ($form_score->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.CannotUpdateFinalizedForm'));
        $form_data = $request->all();
        // We don't need token
        unset($form_data['_token']);
        $camper = $registration->camper;
        $answers = Answer::where('question_set_id', $question_set_id)->where('registration_id', $registration->id)->where('camper_id', $camper->id);
        if ($answers->doesntExist())
            throw new \CampPASSException(trans('exception.NoAnswersSaved'));
        // For all answers given, update all scores of those that will be manually graded
        $answers = $answers->get();
        foreach ($form_data as $id => $value) {
            if (substr($id, 0, 13) === 'manual_score_') {
                $key = substr($id, 13);
                $answer = $answers->filter(function ($answer) use (&$key) {
                    return $answer->question->json_id == $key;
                })->first();
                if (!$answer) {
                    logger()->error('Trying to parse an answer that does not exist.');
                    continue;
                }
                $answer->update([
                    'score' => (double)$value,
                ]);
            }
        }
        return redirect()->back()->with('success', 'Scores are updated successfully.');
    }

    public static function form_finalize(FormScore $form_score, bool $silent = false)
    {
        Common::authenticate_camp($form_score->question_set->camp);
        if ($form_score->finalized)
            throw new \CampPASSExceptionRedirectBack();
        if ($form_score->registration->unsubmitted())
            throw new \CampPASSExceptionRedirectBack(trans('exception.CannotFinalizeUnsubmittedForm'));
        $form_score->update([
            'finalized' => true,
        ]);
        if (!$silent)
            return redirect()->back()->with('success', 'This form is finalized.');
    }

    public static function form_check_real(FormScore $form_score, $checked)
    {
        Common::authenticate_camp($form_score->question_set->camp);
        $form_score->update([
            'checked' => $checked == 'true',
        ]);
    }

    public static function form_check(Request $request)
    {
        $success = true;
        try {
            $content = json_decode($request->getContent(), true);
            $form_score = FormScore::findOrFail($content['form_score_id']);
            self::form_check_real($form_score, $content['checked'] ? 'true' : 'false');
        } catch (\Exception $e) {
            $success = false;
        }
        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    public static function form_pass_real(FormScore $form_score, $checked)
    {
        Common::authenticate_camp($form_score->question_set->camp);
        $form_score->update([
            'passed' => $checked == 'true',
        ]);
    }

    public static function form_pass(Request $request)
    {
        $success = true;
        try {
            $content = json_decode($request->getContent(), true);
            $form_score = FormScore::findOrFail($content['form_score_id']);
            self::form_pass_real($form_score, $content['passed'] ? 'true' : 'false');
        } catch (\Exception $e) {
            $success = false;
        }
        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }
}
