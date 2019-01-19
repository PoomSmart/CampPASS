<?php

namespace App\Enums;

use App\Enums\BasicEnum;

final class QuestionType extends BasicEnum
{
    const
    	TEXT = 1,
    	PARAGRAPH = 2,
    	CHOICES = 3,
    	CHECKBOXES = 4,
        FILE = 5
    ;
}
