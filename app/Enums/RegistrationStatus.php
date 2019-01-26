<?php

namespace App\Enums;

use App\Enums\BasicEnum;

final class RegistrationStatus extends BasicEnum
{
    const
		DRAFT = 1, // The application form is created but not sent
		RETURNED = 2, // The application form needs to be corrected and resubmitted
    	APPLIED = 3, // The application form is submitted to the system
    	APPROVED = 4, // The application form is approved by the system and/or the camp maker
        QUALIFIED = 5 // The camper is fully qualified for joining the camp
    ;
}
