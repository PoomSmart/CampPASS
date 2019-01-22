<?php

namespace App\Http\Controllers;

use App\Camp;

use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;

class CampApplicationController extends Controller
{
    public function landing(Camp $camp)
    {
        // TODO: utilize camper eligibility check
        // TODO: add application date check
        // TODO: add already-applied state check
        $eligible = \Auth::user()->isEligibleForCamp($camp);
        $quota_exceed = $camp->campers(RegistrationStatus::APPROVED)->count() >= $camp->quota;
        return view('camp_application.landing', compact('camp', 'eligible', 'quota_exceed'));
    }
}
