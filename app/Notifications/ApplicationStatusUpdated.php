<?php

namespace App\Notifications;

use App\Common;
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
        $camp_text = Common::getLocalizedName($registration->camp);
        if ($registration->returned)
            return trans('registration.ApplicationFormReturned', ['camp' => $camp_text]);
        switch ($registration->status) {
            case ApplicationStatus::CHOSEN:
                return trans('registration.CamperChosen', ['camp' => $camp_text]);
            case ApplicationStatus::INTERVIEWED:
                return trans('registration.CamperInterviewPassed', ['camp' => $camp_text]);
            case ApplicationStatus::APPROVED:
                return trans('registration.AttendanceConfirm', ['camp' => $camp_text]);
            case ApplicationStatus::REJECTED:
                return trans('registration.CamperRejected', ['camp' => $camp_text]);
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
