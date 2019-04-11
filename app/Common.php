<?php

namespace App;

use App\User;

use App\Enums\QuestionType;

use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\CamperStatusChanged;

use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Common
{
    public static $north_region = [
        50, 51, 52, 53, 54, 55, 56, 57, 58,
    ];

    public static $northeast_region = [
        30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
    ];

    public static $central_region = [
        10, 11, 12, 13, 14, 15, 16, 17, 18, 26, 60, 61, 62, 64, 65, 66, 67, 72, 73, 74, 75,
    ];

    public static $east_region = [
        20, 21, 22, 23, 24, 25, 27,
    ];

    public static $west_region = [
        63, 70, 71, 76, 77,
    ];

    public static $south_region = [
        80, 81, 82, 83, 84, 85, 86, 90, 91, 92, 93, 94, 95, 96,
    ];

    public static $th_months = [
        0, 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];

    public static function randomFrequentHit()
    {
        return rand(0, 10) > 3;
    }

    public static function randomVeryFrequentHit()
    {
        return rand(0, 10) > 1;
    }

    public static function randomMediumHit()
    {
        return rand() & 1;
    }

    public static function randomRareHit()
    {
        return rand(0, 10) > 6;
    }

    public static function randomVeryRareHit()
    {
        return rand(0, 10) > 8;
    }

    public static function campDirectory(int $camp_id)
    {
        return "camps/{$camp_id}";
    }

    public static function paymentDirectory(int $camp_id)
    {
        return self::campDirectory($camp_id).'/payments';
    }

    public static function consentDirectory(int $camp_id)
    {
        return self::campDirectory($camp_id).'/consents';
    }

    public static function publicCampDirectory(int $camp_id)
    {
        return "public/camps/{$camp_id}";
    }

    public static function userDirectory(int $user_id)
    {
        return "public/users/{$user_id}";
    }

    public static function userFileDirectory(int $user_id)
    {
        return self::userDirectory($user_id).'/files';
    }

    public static function registrationDirectory(int $camp_id)
    {
        return self::campDirectory($camp_id).'/registrations';
    }

    public static function randomString(int $length = 6)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function randomInt10()
    {
        return rand(1, 10);
    }

    public static function randomElement(array $array)
    {
        return $array[array_rand($array)];
    }

    public static function downloadFile($path)
    {
        try {
            return Storage::download($path);
        } catch (\Exception $e) {
            throw new \CampPASSExceptionRedirectBack(trans('exception.FileNotFound'));
        }
    }

    public static function deleteFile($path, $filename = null)
    {
        if (!$path || !Storage::delete($path)) {
            if (!$filename) $filename = trans('app.SpecifiedDocument');
            throw new \CampPASSExceptionRedirectBack(trans('app.FileNotRemoved', ['filename' => $filename]));
        }
        return redirect()->back()->with('success', trans('app.FileRemoved', ['filename' => $filename]));
    }

    public static function getLocalizedName($record, string $attribute = 'name', string $forced_lang = null)
    {
        if ($forced_lang && in_array($forced_lang, config('app.locales')))
            return $record->{"{$attribute}_{$forced_lang}"};
        $th = $record->{"{$attribute}_th"};
        $en = $record->{"{$attribute}_en"};
        if ((app()->getLocale() == 'th' && !is_null($th)) || is_null($en))
            return $th;
        return $en ? $en : "<blank>";
    }

    public static function unsortedValues($clazz, string $column = null, string $value = null, string $group = null)
    {
        if ($column && $value) {
            $values = $clazz::where($column, $value);
            if ($group)
                $values = $values->where($group, '<>', '')->get()->unique($group);
            else
                $values = $values->get();
        } else
            $values = $clazz::all();
        return $values;
    }

    public static function values($clazz, string $column = null, string $value = null, string $group = null)
    {
        return Arr::sort(self::unsortedValues($clazz, $column, $value, $group), function($record) {
            return $record->__toString();
        });
    }

    /**
     * Check whether the given camp can be manipulated by the current user.
     *
     */
    public static function authenticate_camp(Camp $camp)
    {
        $user = auth()->user();
        if ($user) {
            if (!$camp->approved && !$user->hasRole('admin'))
                throw new \App\Exceptions\ApproveCampException();
            $user->canManageCamp($camp);
        }
    }

    public static function maxPagination()
    {
        return config('const.app.max_paginate');
    }

    public static function withPagination($view, $request = null)
    {
        $max = self::maxPagination();
        if (!$request)
            $request = request();
        return $view->with('i', ($request->input('page', 1) - 1) * $max);
    }

    public static function admin()
    {
        return User::where('type', config('const.account.admin'))->limit(1)->first();
    }

    public static function formattedDate($date, bool $time = false, int $addedDays = 0)
    {
        $locale = app()->getLocale();
        $month = $locale == 'th' ? '[%#m]' : '%B';
        if (!($date instanceof Carbon))
            $date = Carbon::parse($date);
        if ($addedDays)
            $date = $date->addDays($addedDays);
        $year = $date->year;
        if ($locale == 'th')
            $year = ($year + 543) % 100;
        $formatted = $date->formatLocalized("%e {$month} {$year}".($time ? ", %H:%m" : ''));
        if ($locale == 'th')
            $formatted = str_replace("[$date->month]", self::$th_months[$date->month], $formatted);
        return $formatted;
    }

    public static function readableNotificationType($type)
    {
        if (CamperStatusChanged::class == $type)
            return trans('app.Application');
        if (ApplicationStatusUpdated::class == $type)
            return trans('registration.Status');
        return trans('app.Other');
    }
}