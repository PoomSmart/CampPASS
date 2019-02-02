<?php

namespace App\Exceptions;

use Exception;

class CampPASSException extends Exception
{
    protected function setMessage($message)
    {
        $this->message = $message ? $message : trans('app.NoPermissionError');
    }
}
