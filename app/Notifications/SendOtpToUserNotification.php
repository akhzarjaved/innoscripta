<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpToUserNotification extends Notification
{
    public function __construct(
        public $otp
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('You are receiving this email because we received a password reset request for your account. Use the below OTP for verification')
            ->line($this->otp)
            ->line('If you did not request a password reset, no further action is required.')
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
