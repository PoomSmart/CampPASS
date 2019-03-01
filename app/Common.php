<?php

namespace App;

use App\Enums\QuestionType;

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

    public static function userDirectory(int $user_id)
    {
        return "users/{$user_id}";
    }

    public static function fileDirectory(int $user_id)
    {
        return self::userDirectory($user_id)."/files";
    }

    public static function registrationDirectory(int $camp_id)
    {
        return self::campDirectory($camp_id)."/registrations";
    }

    public static function randomString(int $length = 6)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function randomInt10()
    {
        return rand(1, 10);
    }

    public static function getLocalizedName($record, string $attribute = 'name')
    {
        $th = $record->{"{$attribute}_th"};
        $en = $record->{"{$attribute}_en"};
        if ((\App::getLocale() == 'th' && !is_null($th)) || is_null($en))
            return $th;
        return $en ? $en : "<blank>";
    }

    public static function values($clazz, string $column = null, string $value = null, string $group = null)
    {
        if ($column && $value) {
            $values = $clazz::where($column, $value);
            if ($group)
                $values = $values->where($group, '<>', '')->get()->unique($group);
            else
                $values = $values->get();
        }else
            $values = $clazz::all();
        return Arr::sort($values, function($record) {
            return $record->__toString();
        });
    }

    /**
     * Check whether the given camp can be manipulated by the current user.
     * 
     */
    public static function authenticate_camp(Camp $camp, bool $silent = false)
    {
        if (!$silent) {
            $user = \Auth::user();
            if (!$camp->approved && !$user->hasRole('admin'))
                throw new \App\Exceptions\ApproveCampException();
            $user->canManageCamp($camp);
        }
    }
}