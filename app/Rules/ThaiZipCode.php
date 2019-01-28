<?php

namespace App\Rules;

use App\Common;

use Illuminate\Contracts\Validation\Rule;

class ThaiZipCode implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $prefix = (int)substr($value, 0, 2);
        if (!in_array($prefix, Common::$west_region) && !in_array($prefix, Common::$east_region) && !in_array($prefix, Common::$north_region)
            && !in_array($prefix, Common::$south_region) && !in_array($prefix, Common::$central_region) && !in_array($prefix, Common::$northeast_region))
            return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.regex');
    }
}
