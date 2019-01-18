<?php

namespace App\Enums;

use App\Enums\BasicEnum;

final class CandidateStatus extends BasicEnum
{
    const
    	CHOSEN = 1,
    	CONFIRMED = 2,
    	REFUSED = 3,
        SUBSTITUTE = 4
    ;
}
