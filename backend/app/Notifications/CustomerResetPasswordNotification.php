<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerResetPasswordNotification extends Notification
{
    protected $token;

    /**
     * Constructor để truyền token.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Chỉ định kênh gửi notification.
     */
    public function via($notifiable)
    {
        return ['mail']; // Gửi qua email
    }

    /**
     * Tạo nội dung email gửi cho người dùng.
     */
    public function toMail($notifiable)
    {
        $resetUrl = url('/password/reset/' . $this->token . '?email=' . urlencode($notifiable->email));

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
