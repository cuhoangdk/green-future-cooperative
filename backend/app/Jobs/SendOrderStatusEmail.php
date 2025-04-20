<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendOrderStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $recipientType;

    public function __construct(Order $order, string $recipientType)
    {
        $this->order = $order;
        $this->recipientType = $recipientType;
    }

    public function handle()
    {
        \Log::info("Sending email for order {$this->order->id} to {$this->recipientType}");
        $email = new OrderStatusUpdated($this->order, $this->recipientType);
        
        switch ($this->recipientType) {
            case 'customer':
                if ($this->order->customer && $this->order->customer->email) {
                    Mail::to($this->order->customer->email)->send($email);
                } elseif ($this->order->email) {
                    Mail::to($this->order->email)->send($email);
                }
                break;
            case 'seller':
                $sellers = $this->order->items->map(fn($item) => $item->product->user->email ?? null)->filter()->unique();
                foreach ($sellers as $sellerEmail) {
                    Mail::to($sellerEmail)->send($email);
                }
                break;
            case 'super_admin':
                $superAdmins = User::where('is_super_admin', true)->pluck('email')->filter();
                foreach ($superAdmins as $superAdminEmail) {
                    Mail::to($superAdminEmail)->send($email);
                }
                break;
        }
    }
}