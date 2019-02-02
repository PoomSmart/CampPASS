<?php

namespace App\Exceptions;

use \App\Exceptions\CampPASSException;

class ApproveCampException extends CampPASSException
{
    protected function setMessage($message)
    {
        $this->message = trans('camp.ApproveFirst');
    }
}
