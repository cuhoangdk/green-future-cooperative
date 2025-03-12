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
     * Chỉ định kênh gửi thông báo.
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
        $url = url('/password/reset/' . $this->token . '?email=' . urlencode($notifiable->email));

        return (new MailMessage)
            ->subject('Thông Báo Đặt Lại Mật Khẩu')
            ->view(
                'emails.reset-password', // Tên view
                ['url' => $url] // Dữ liệu truyền vào view
            );
    }
}