<?php
namespace App\Jobs;

use App\Models\User;
use App\Notifications\CustomerResetPasswordNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customer;
    protected $token;

    public function __construct(User $customer, string $token)
    {
        $this->customer = $customer;
        $this->token = $token;
    }

    public function handle(): void
    {
        $this->customer->notify(new ResetPasswordNotification($this->token));
    }
}
