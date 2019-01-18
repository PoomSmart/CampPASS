<?php

namespace App\Enums;

use App\Enums\BasicEnum;

final class RegistrationStatus extends BasicEnum
{
    const
    	DRAFT = 1,
    	APPLIED = 2,
    	RETURNED = 3,
    	APPROVED = 4,
        QUALIFIED = 5
    ;
}
