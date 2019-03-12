<?php

namespace App\Notifications;

use App\Camp;

class NewCampRegistered extends LocalizableNotification
{
    protected $camp;

    public function __construct(Camp $camp)
    {
        $this->camp = $camp;
    }

    public function toText(Camp $camp)
    {
        return trans('camp.WaitForApproval', ['camp' => $camp]);
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
