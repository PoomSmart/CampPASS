<?php

namespace App\Http\Controllers;

use App\Camp;
use App\Common;
use App\Registration;

use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampApplicationController extends Controller
{
    public function landing(Camp $camp)
    {
        // Stage 1: Answering questions
        // TODO: make $operate less "elegant" and more "readable?"
        // TODO: add application date check
        $user = \Auth::user();
        // TODO: verify camper eligibility check
        $operate = $eligible = $user->isEligibleForCamp($camp);
        $latest_registration = $operate ? $camp->registrations()->where('camper_id', $user->id)->latest()->first() : null;
        // TODO: verify already-applied state check
        $already_applied = $latest_registration ? $latest_registration->status == RegistrationStatus::QUALIFIED : false;
        $operate = $operate && !$already_applied ? true : false;
        // TODO: verify quota exceed check
        $quota_exceed = $operate && $camp->quota && $camp->campers(RegistrationStatus::APPROVED)->count() >= $camp->quota;
        $operate = $operate && !$quota_exceed ? true : false;
        $question_set = $operate ? $camp->questionSet() : null;
        $questions = [];
        if ($question_set) {
            $pairs = $question_set->pairs()->get();
            foreach ($pairs as $pair)
                array_push($questions, $pair->question());
        }
        $json = [];
        if ($operate && $eligible && !$quota_exceed && !empty($questions)) {
            $json_path = Common::questionSetDirectory($camp->id).'/questions.json';
            $json = json_decode(Storage::disk('local')->get($json_path), true);
            // Remove solutions from the questions
            unset($json['radio']);
            unset($json['checkbox']);
        }
        return view('camp_application.question_answer', compact('camp', 'eligible', 'quota_exceed', 'already_applied', 'questions', 'json'));
    }

    public function store(Request $request)
    {
        dd($request->all());
    }
}
