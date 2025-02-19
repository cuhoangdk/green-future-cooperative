<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyCustomerAccount extends Notification
{
    use Queueable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = config('app.url') . "/api/customer-auth/verify-account?token={$this->token}&email={$notifiable->email}";

        return (new MailMessage)
            ->subject('Verify Your Account')
            ->greeting('Hello!')
            ->line('Thank you for registering. Please verify your email address.')
            ->action('Verify Email', $url)
            ->line('If you did not create an account, no further action is required.');
    }
}
