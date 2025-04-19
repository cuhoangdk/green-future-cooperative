<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $recipientType;

    public function __construct(Order $order, $recipientType)
    {
        $this->order = $order;
        $this->recipientType = $recipientType;
    }

    public function build()
    {
        $statusTranslations = [
            'pending' => 'Đang chờ xử lý',
            'processing' => 'Đang xử lý',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy',
            'return' => 'Trả hàng',
        ];
        $translatedStatus = $statusTranslations[$this->order->status] ?? $this->order->status;

        $subject = "Cập Nhật Trạng Thái Đơn Hàng #{$this->order->id} - {$translatedStatus}";

        return $this->subject($subject)
                    ->view('emails.order_status_updated')
                    ->with([
                        'order' => $this->order,
                        'recipientType' => $this->recipientType,
                    ]);
    }
}