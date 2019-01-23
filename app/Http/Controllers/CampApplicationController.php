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
    public function getLatestRegistration(Camp $camp, $camper_id)
    {
        return $camp->registrations()->where('camper_id', $camper_id)->latest()->first();
    }

    public function isCampFull(Camp $camp)
    {
        return $camp->quota && $camp->campers(RegistrationStatus::APPROVED)->count() >= $camp->quota;
    }

    public function camperAlreadyApplied($registration)
    {
        return $registration ? $registration->status == RegistrationStatus::QUALIFIED : false;
    }

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
        // Stage 1: Answering questions
        // $camp->camp_procedure()->candidate_required);
        // TODO: make $operate less "elegant" and more "readable?"
        // TODO: add application date check
        $user = \Auth::user();
        // TODO: verify camper eligibility check
        $operate = $eligible = $user->isEligibleForCamp($camp);
        $latest_registration = $operate ? $this->getLatestRegistration($camp, $user->id) : null;
        // TODO: verify already-applied state check
        $already_applied = $this->camperAlreadyApplied($latest_registration);
        $operate = $operate && !$already_applied ? true : false;
        // TODO: verify quota exceed check
        $quota_exceed = $operate && $this->isCampFull($camp);
        $operate = $operate && !$quota_exceed ? true : false;
        $question_set = $operate ? $camp->question_set() : null;
        $questions = [];
        if ($question_set) {
            $pairs = $question_set->pairs()->get();
            foreach ($pairs as $pair) {
                $question = $pair->question();
                array_push($questions, $question);
            }
        }
        $json = [];
        $answers = [];
        if ($operate && $eligible && !$quota_exceed && !empty($questions)) {
            $json_path = Common::questionSetDirectory($camp->id).'/questions.json';
            $json = json_decode(Storage::disk('local')->get($json_path), true);
            // Remove solutions from the questions before responding back to campers
            unset($json['radio']);
            unset($json['checkbox']);
            $pre_answers = Answer::where('question_set_id', $question_set->id)->where('camper_id', $user->id)->get(['question_id', 'answer']);
            foreach ($pre_answers as $pre_answer) {
                $question = Question::find($id = $pre_answer->question_id);
                $key = $question->json_id;
                $answers[$key] = $this->decodeIfNeeded($pre_answer->answer, $question->type);
            }
        }
        return view('camp_application.question_answer', compact('camp', 'eligible', 'quota_exceed', 'already_applied', 'questions', 'answers', 'json'));
    }

    private function encodeIfNeeded($value, $question_type)
    {
        if ($question_type == QuestionType::CHECKBOXES)
            return json_encode($value);
        return $value;
    }

    private function decodeIfNeeded($value, $question_type)
    {
        if ($question_type == QuestionType::CHECKBOXES)
            return json_decode($value);
        return $value;
    }

    public function store(Request $request)
    {
        $camp = Camp::find($request->input('camp_id'));
        if (strcmp(get_class($camp), 'App\Camp')) return $camp;
        // Campers would not submit the answers to the questions of such non-approved camps
        if (!$camp->approved && !\Auth::user()->hasRole('admin'))
            return redirect('/')->with('error', 'Unable to save the answers.');
        $user = \Auth::user();
        // A registration record will be created if not already
        $registration_id = -1;
        $registration = $this->getLatestRegistration($camp, $user->id);
        if ($registration)
            $registration_id = $registration->id;
        else {
            $registration_id = Registration::create([
                'camp_id' => $camp->id,
                'camper_id' => $user->id,
            ])->id;
        }
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
                'registration_id' => $registration_id,
            ], [
                'answer' => $this->encodeIfNeeded($request[$json_id], $question->type),
            ]);
        }
        return redirect()->back()->with('success', 'Answers are saved.');
    }
}
