<?php

namespace App\Notifications;

use App\User;

class UserRegisteredSuccessfully extends LocalizableNotification
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toMail($notifiable)
    {
        /** @var User $user */
        $user = $this->user;

        return (new MailMessage)
                    ->from(env('ADMIN_MAIL'))
                    ->subject(trans('account.SuccessfullyCreatedAccountTitle'))
                    ->greeting(sprintf(trans('message.Hello %s', $user->getFullName())))
                    ->line(trans('account.NewAccountCreated'))
                    ->action(trans('message.Click Here', route('activate.user', $user->activation_code)))
                    ->line(trans('message.ThankYouUsing'));
    }
}
