<?php

namespace App\Http\Controllers;

use App\Camp;

use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;

class CampApplicationController extends Controller
{
    public function landing(Camp $camp)
    {
        // TODO: add application date check
        $user = \Auth::user();
        // TODO: verify camper eligibility check
        $eligible = $user->isEligibleForCamp($camp);
        $latest_registration = $camp->registrations()->where('camper_id', $user->id)->latest()->first();
        // TODO: verify already-applied state check
        $already_applied = $latest_registration ? $latest_registration->status == RegistrationStatus::QUALIFIED : false;
        // TODO: verify quota exceed check
        $quota_exceed = $camp->quota && $camp->campers(RegistrationStatus::APPROVED)->count() >= $camp->quota;
        $question_set = $camp->questionSet();
        $questions = [];
        if ($question_set) {
            $pairs = $question_set->pairs()->get();
            foreach ($pairs as $pair)
                array_push($questions, $pair->question());
        }
        return view('camp_application.landing', compact('camp', 'eligible', 'quota_exceed', 'already_applied', 'questions'));
    }
}
