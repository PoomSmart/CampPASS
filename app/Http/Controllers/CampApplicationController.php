<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Camp;
use App\Common;
use App\Registration;
use App\Question;
use App\QuestionSet;
use App\QuestionSetQuestionPair;

use App\Http\Controllers\QuestionController;

use App\Enums\QuestionType;
use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampApplicationController extends Controller
{
    /**
     * Check whether the given camp can be manipulated by the current user.
     * The function returns the camp object if the user can.
     * 
     */
    public static function authenticate($camp_id)
    {
        $camp = Camp::find($camp_id);
        if (!$camp->approved && !\Auth::user()->hasRole('admin'))
            return redirect('/')->with('error', trans('camp.ApproveFirst'));
        if (!\Auth::user()->canManageCamp($camp))
            return redirect('/')->with('error', trans('app.NoPermissionError'));
        return $camp;
    }

    public function landing(Camp $camp)
    {
        if ($camp->camp_procedure()->candidate_required) {
            // Stage: Answering questions
            $user = \Auth::user();
            // TODO: verify camper eligibility check
            $already_applied = $user->alreadyAppliedForCamp($camp);
            if ($already_applied)
                return view('camp_application.question_answer', compact('already_applied'));
            $ineligible_reason = $user->getIneligibleReasonForCamp($camp);
            if ($ineligible_reason)
                return view('camp_application.question_answer', compact('ineligible_reason'));
            $json = [];
            $answers = [];
            $question_set = $camp->question_set();
            $pairs = $question_set ? $question_set->pairs()->get() : [];
            if (!empty($pairs)) {
                $json = Common::getQuestionJSON($camp->id);
                $pre_answers = Answer::where('question_set_id', $question_set->id)->where('camper_id', $user->id)->get(['question_id', 'answer']);
                foreach ($pre_answers as $pre_answer) {
                    $question = Question::find($id = $pre_answer->question_id);
                    $key = $question->json_id;
                    $answers[$key] = Common::decodeIfNeeded($pre_answer->answer, $question->type);
                }
            }
            return view('camp_application.question_answer', compact('camp', 'answers', 'json', 'question_set'));
        }
        return null;
    }

    public function store(Request $request)
    {
        $camp = Camp::find($request->input('camp_id'));
        if (strcmp(get_class($camp), 'App\Camp')) return $camp;
        // Campers would not submit the answers to the questions of such non-approved camps
        if (!$camp->approved && !\Auth::user()->hasRole('admin'))
            return redirect('/')->with('error', 'Unable to save the answers.');
        $user = \Auth::user();
        // In case campers somehow want to edit the answers in the submitted application form
        if ($user->alreadyAppliedForCamp($camp))
            return redirect('/')->with('error', 'Unable to save the answers.');
        // A registration record will be created if not already
        $registration = $camp->getLatestRegistration($user->id);
        if (!$registration)
            $registration = Registration::create([
                'camp_id' => $camp->id,
                'camper_id' => $user->id,
            ]);
        // Get the corresponding question set for this camp, then reference it to creating or updating answers as needed
        $question_set = QuestionSet::where('camp_id', $camp->id)->first();
        $question_ids = $question_set->pairs()->get(['question_id']);
        $questions = Question::whereIn('id', $question_ids)->get();
        foreach ($questions as $question) {
            $json_id = $question->json_id;
            Answer::updateOrCreate([
                'question_set_id' => $question_set->id,
                'question_id' => $question->id,
                'camper_id' => $user->id,
                'registration_id' => $registration->id,
            ], [
                'answer' => Common::encodeIfNeeded($request[$json_id], $question->type),
            ]);
        }
        return redirect()->back()->with('success', 'Answers are saved.');
    }

    public function answer_view(QuestionSet $question_set)
    {
        $camper = \Auth::user();
        $camp = $question_set->camp();
        $pairs = $question_set ? $question_set->pairs()->get() : [];
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id);
        foreach ($pairs as $pair) {
            $question = $pair->question();
            $answer = $question_set->answers()->where('camper_id', $camper->id)->where('question_id', $question->id)->get();
            if ($answer->isNotEmpty())
                $answer = $answer->first()->answer;
            else
                $answer = '';
            $data[] = [
                'question' => $question,
                'answer' => Common::decodeIfNeeded($answer, $question->type),
            ];
        }
        return view('camp_application.answer_view', compact('data', 'json', 'question_set', 'camp'));
    }

    public function submit_application_form(Camp $camp)
    {
        $registration = $camp->getLatestRegistration(\Auth::user()->id);
        if ($registration->cannotSubmit()) {
            // This should not happen
            return redirect()->back()->with('error', 'You cannot submit the application form to the camp you alraedy are qualified for.');
        }
        $registration->status = RegistrationStatus::APPLIED;
        $registration->save();
        return view('camp_application.done');
    }
}
