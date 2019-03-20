<?php

namespace App\Notifications;

use App\Registration;

use App\Enums\ApplicationStatus;

class ApplicationStatusUpdated extends LocalizableNotification
{
    protected $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function toText(Registration $registration)
    {
        if ($registration->returned)
            return trans('qualification.ApplicationFormReturned', ['camp' => $registration->camp]);
        switch ($registration->status) {
            case ApplicationStatus::CHOSEN:
            case ApplicationStatus::APPROVED:
                return trans('qualification.AttendanceConfirm', ['camp' => $registration->camp]);
            case ApplicationStatus::REJECTED:
                return trans('qualification.Disqualified', ['camp' => $registration->camp]);
            default:
                return 'Undefined';
        }
    }

    public function toURL(Registration $registration)
    {
        return route('camp_application.status', $registration->id);
    }

    public function toDatabase($notifiable)
    {
        return [
            'registration_id' => $this->registration->id,
            'status' => $this->registration->status,
            'content' => $this->toLocalizedText($this->registration),
            'url' => $this->toURL($this->registration),
        ];
    }
}
