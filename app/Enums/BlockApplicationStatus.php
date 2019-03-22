<?php

namespace App\Enums;

use App\Enums\BasicEnum;

final class BlockApplicationStatus extends BasicEnum
{
    const
			APPLICATION = 1,
			INTERVIEW = 2,
			PAYMENT = 3,
			APPROVAL = 4,
			CONFIRMATION = 5
    ;
}
