<?php

namespace App\Notifications;

use App\Registration;

class NewCamperApplied extends LocalizableNotification
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
        return trans('camp.NewCamperApplied', [
            'camper' => $this->camper, 'camp' => $this->camp,
        ]);
    }

    public function toURL()
    {
        return route('profiles.show', $this->camper->id);
    }

    public function toDatabase($notifiable)
    {
        return [
            'registration_id' => $this->registration->id,
            'content' => $this->toLocalizedText($this->registration),
            'url' => $this->toURL(),
        ];
    }
}
