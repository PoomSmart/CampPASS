<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocalizableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toLocalizedText($record)
    {
        $original_locale = app()->getLocale();
        $text = [];
        foreach (['th', 'en'] as $locale) {
            app()->setLocale($locale);
            $text[$locale] = $this->toText($record);
        }
        app()->setLocale($original_locale);
        return $text;
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
