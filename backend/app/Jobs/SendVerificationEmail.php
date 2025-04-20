<?php
namespace App\Jobs;

use App\Models\Customer;
use App\Notifications\VerifyCustomerAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customer;
    protected $token;

    public function __construct(Customer $customer, string $token)
    {
        $this->customer = $customer;
        $this->token = $token;
    }

    public function handle()
    {
        $this->customer->notify(new VerifyCustomerAccount($this->token));
    }
}
