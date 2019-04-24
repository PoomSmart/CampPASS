<?php

namespace App\Notifications;

use App\Common;
use App\Registration;

use App\Enums\ApplicationStatus;

class CamperStatusChanged extends LocalizableNotification
{
    protected $registration, $camp, $camper;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
        $this->camper = $registration->camper;
        $this->camp = $registration->camp;
    }

    public function toText(Registration $registration)
    {
        $compact = [
            'camper' => $this->camper->getFullName(), 'camp' => Common::getLocalizedName($this->camp),
        ];
        switch ($registration->status) {
            case ApplicationStatus::WITHDRAWN:
                return trans('camp.CamperWithdrawn', $compact);
        }
        return trans('camp.NewCamperApplied', $compact);
    }

    public function toURL()
    {
        return route('camps.registration', $this->camp->id);
    }

    public function toDatabase($notifiable)
    {
        return [
            'registration_id' => $this->registration->id,
            'camper_id' => $this->camper->id,
            'camp_id' => $this->camp->id,
            'content' => $this->toLocalizedText($this->registration),
            'url' => $this->toURL(),
        ];
    }
}
