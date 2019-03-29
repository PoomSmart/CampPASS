<?php

namespace App\Enums;

use App\Enums\BasicEnum;

final class ApplicationStatus extends BasicEnum
{
	const
			// The camper has not submitted but saved on the server
			DRAFT = 1,
			// The camper has been rejected from the camp due to either of the followings:
			// (1) The total score doesn't exceed the minimum criteria
			// (2) The confirmation or documents uploading were not done in time
			REJECTED = 2,
			// The camper voluntarily withdrawed from the camp, no longer being able to apply for again
			WITHDRAWED = 3,
			// The camper has submitted the form to the server, no longer being able to make changes to the form
			APPLIED = 4,
			// The camper has been chosen as a candidate as the total score exceeds the minimum, OR
			// The camper applied for the camp of deposit-only type
			CHOSEN = 5,
			// The camper has passed the interview of the camp
			INTERVIEWED = 6,
			// The camper has been approved as their documents (including payment slips) are all correct
			APPROVED = 7,
			// The camper has confirmed that they will attend the camp
			CONFIRMED = 8
    ;
}
