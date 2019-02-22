<?php

namespace App\Enums;

use ReflectionClass;

abstract class BasicEnum {
    private static $constCacheArray = NULL;

    public static function getConstants()
    {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function any()
    {
        $constants = self::getConstants();
        $index = array_rand($constants);
        return $constants[$index];
    }

    public static function getLocalizedConstants($lang_prefix = 'app')
    {
        $localized = [];
        $constants = self::getConstants();
        foreach ($constants as $text => $number) {
            array_push($localized, (object)[ 'id' => $number, 'value' => $number, 'name' => trans("{$lang_prefix}.{$text}") ]);
        }
        return $localized;
    }

    public static function isValidName($name, $strict = false)
    {
        $constants = self::getConstants();
        if ($strict)
            return array_key_exists($name, $constants);
        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value, $strict = true)
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }
}