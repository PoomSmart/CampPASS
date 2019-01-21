<?php

namespace App\Http\Controllers;

use App\Camp;

use Illuminate\Http\Request;

class CampApplicationController extends Controller
{
    public function landing(Camp $camp)
    {
        // TODO: utilize camper eligibility check
        // \Auth::user()->isEligibleForCamp($camp)
        // TODO: add application date check
        // TODO: add quota check
        // TODO: add already-applied state check
        return view('camp_application.landing', compact('camp'));
    }
}
