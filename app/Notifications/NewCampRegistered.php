<?php

namespace App\Notifications;

use App\Camp;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewCampRegistered extends Notification
{
    use Queueable;

    protected $camp;

    public function __construct(Camp $camp)
    {
        $this->camp = $camp;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toText(Camp $camp)
    {
        return "{$camp} is waiting for your approval.";
    }

    public function toURL(Camp $camp)
    {
        return route('camps.show', $camp->id);
    }

    public function toDatabase($notifiable)
    {
        return [
            'camp_id' => $this->camp->id,
            'content' => $this->toText($this->camp),
            'url' => $this->toURL($this->camp),
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
