<?php

namespace App;

use App\Registration;

use App\Enums\RegistrationStatus;

class BadgeController
{
    public static function addBadgeIfNeeded(Registration $registration)
    {
        if ($registration->applied_or_qualified()) { // TODO: change back
            $camper = $registration->camper();
            $camp = $registration->camp();
            $registrations = $camp->getRegistrations($camper)->where('status', RegistrationStatus::APPLIED)->get(); // QUALIFIED
            $registrations->chunk(5, function ($chunk) {
                foreach ($chunk as $registration) {
                    dd($registration);
                }
            });
            $badges = Badge::where('camper_id', $camper->id)->where('camp_id', $camp->id)->get();
            dd($badges);
        }
    }
}