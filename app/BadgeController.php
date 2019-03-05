<?php

namespace App;

use App\Badge;
use App\BadgeCategory;
use App\Registration;

use App\Enums\ApplicationStatus;

class BadgeController
{
    protected static $premiumBadgeID, $babyStepBadgeID, $pioneerBadgeID;

    public static function getPremiumBadgeID()
    {
        if (self::$premiumBadgeID)
            return self::$premiumBadgeID;
        self::$premiumBadgeID = BadgeCategory::where('name', 'Premium')->limit(1)->first()->id;
        return self::$premiumBadgeID;
    }

    public static function getBabyStepBadgeID()
    {
        if (self::$babyStepBadgeID)
            return self::$babyStepBadgeID;
        self::$babyStepBadgeID = BadgeCategory::where('name', 'BabyStep')->limit(1)->first()->id;
        return self::$babyStepBadgeID;
    }

    public static function getPioneerBadgeID()
    {
        if (self::$pioneerBadgeID)
            return self::$pioneerBadgeID;
        self::$pioneerBadgeID = BadgeCategory::where('name', 'Pioneer')->limit(1)->first()->id;
        return self::$pioneerBadgeID;
    }

    public static function addBadgeIfNeeded(Registration $registration)
    {
        // TODO: Add Pioneer Badge
        // TODO: Further optimization?
        if ($registration->qualified()) {
            $camper = $registration->camper;
            if ($camper->badges()->where('badge_category_id', self::getBabyStepBadgeID())->limit(1)->doesntExist()) {
                // Attended the first camp via CampPASS
                Badge::create([
                    'badge_category_id' => self::getBabyStepBadgeID(),
                    'camper_id' => $camper->id,
                    'earned_date' => now(),
                ]);
            }
            $registrations = $camper->registrations()->where('status', ApplicationStatus::CONFIRMED);
            if ($registrations->count() >= 10) {
                // Attended 10 camps
                if ($camper->badges()->where('badge_category_id', self::getPremiumBadgeID())->limit(1)->doesntExist()) {
                    Badge::create([
                        'badge_category_id' => self::getPremiumBadgeID(),
                        'camper_id' => $camper->id,
                        'earned_date' => now(),
                    ]);
                }
            }
            $registrations_by_camp_categories = [];
            $registrations->chunk(100, function ($chunk) use (&$registrations_by_camp_categories, &$camper) {
                foreach ($chunk as $registration) {
                    $category_id = $registration->camp->camp_category_id;
                    if (!isset($registrations_by_camp_categories[$category_id]))
                        $registrations_by_camp_categories[$category_id] = [];
                    $registrations_by_camp_categories[$category_id][] = $registration;
                    if (count($registrations_by_camp_categories[$category_id]) == 3) {
                        if ($camper->badges->filter(function ($badge) use (&$category_id) {
                            return $badge->badge_category_id == $category_id;
                        })->isEmpty()) {
                            // Attended 3 of the camps of the same category
                            Badge::create([
                                'badge_category_id' => $category_id,
                                'camper_id' => $camper->id,
                                'earned_date' => now(),
                            ]);
                        }
                    }
                }
            });
            unset($registrations_by_camp_categories);
        }
    }
}