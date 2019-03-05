<?php

namespace App\Notifications;

use App\Registration;

use App\Enums\ApplicationStatus;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toText(Registration $registration)
    {
        switch ($registration->status) {
            case ApplicationStatus::CHOSEN:
            case ApplicationStatus::APPROVED:
                return "Congratulations! You are chosen for {$registration->camp}.";
            case ApplicationStatus::REJECTED:
                return "Sorry, you are disqualified for {$registration->camp}.";
            default:
                return "Undefined";
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
            'content' => $this->toText($this->registration),
            'url' => $this->toURL($this->registration),
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
