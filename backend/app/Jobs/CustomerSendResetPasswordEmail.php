<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Notifications\CustomerResetPasswordNotification;
use Carbon\Traits\Serialization;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class CustomerSendResetPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Serialization;

    protected $customer;
    protected $token;

    public function __construct(Customer $customer, string $token)
    {
        $this->customer = $customer;
        $this->token = $token;
    }

    public function handle(): void
    {
        $this->customer->notify(new CustomerResetPasswordNotification($this->token));
    }
}
