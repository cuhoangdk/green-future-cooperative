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
            ->subject('Xác Minh Tài Khoản Của Bạn')
            ->view(
                'emails.verify-account', // Tên view
                ['url' => $url] // Dữ liệu truyền vào view
            );
    }
}