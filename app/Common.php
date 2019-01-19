<?php

namespace App;

class Common
{
    public static function campDirectory($camp_id)
    {
        return "camps/{$camp_id}";
    }

    public static function registrationDirectory($camp_id)
    {
        return self::campDirectory($camp_id)."/registrations";
    }

    public static function questionSetDirectory($camp_id)
    {
        return self::campDirectory($camp_id)."/questions";
    }
}