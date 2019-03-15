<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ThaiCitizenID implements Rule
{
    /**
     * Determine if the validation rule passes.
     * https://www.spicydog.org/blog/php-function-thai-national-id-validation/
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (strlen($value) === 13) {
            $digits = str_split($value);
            $lastDigit = array_pop($digits);
            $sum = array_sum(array_map(function($d, $k) {
                return ($k + 2) * $d;
            }, array_reverse($digits), array_keys($digits)));
            return $lastDigit === strval((11 - $sum % 11) % 10);
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.regex', [
            'attribute' => trans('account.CitizenID')
        ]);
    }
}
