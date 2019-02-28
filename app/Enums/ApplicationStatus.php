<?php

namespace App\Enums;

use App\Enums\BasicEnum;

// TODO: TBD
final class ApplicationStatus extends BasicEnum
{
    const
			DRAFT = 1,
			REJECTED = 2,
			WITHDRAWED = 3,
			APPLIED = 4,
			CHOSEN = 5,
			INTERVIEWED = 6,
			PAID = 7,
			APPROVED = 8,
			QUALIFIED = 9
    ;
}
