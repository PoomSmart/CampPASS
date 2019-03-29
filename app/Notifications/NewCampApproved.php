<?php

namespace App\Notifications;

use App\Common;
use App\Camp;

class NewCampApproved extends LocalizableNotification
{
    protected $camp;
    protected $for_campers;

    public function __construct(Camp $camp, bool $for_campers)
    {
        $this->camp = $camp;
        $this->for_campers = $for_campers;
    }

    public function toText(Camp $camp)
    {
        return trans($this->for_campers ? 'camp.CampAdded' : 'camp.CampHasBeenApproved', [
            'camp' => Common::getLocalizedName($camp),
        ]);
    }

    public function toURL(Camp $camp)
    {
        return route('camps.show', $camp->id);
    }

    public function toDatabase($notifiable)
    {
        return [
            'camp_id' => $this->camp->id,
            'content' => $this->toLocalizedText($this->camp),
            'url' => $this->toURL($this->camp),
        ];
    }
}
