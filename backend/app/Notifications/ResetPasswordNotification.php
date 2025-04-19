<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

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
        $url = env('FRONTEND_URL') . "/admin/reset-password?token={$this->token}&email={$notifiable->email}";

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Thông Báo Đặt Lại Mật Khẩu')
            ->view(
                'emails.reset-password', // Tên view
                ['url' => $url] // Dữ liệu truyền vào view
            );
    }
}