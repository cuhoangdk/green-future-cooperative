<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'full_name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'address_type' => $this->faker->randomElement(['home', 'work', 'other']),           
            'is_default' => false, // Mặc định là không phải địa chỉ chính
        ];
    }

    /**
     * Đặt địa chỉ là mặc định.
     */
    public function default()
    {
        return $this->state(fn () => ['is_default' => true]);
    }
    public function configure()
    {
        return $this->afterCreating(function (CustomerAddress $customerAddress) {
            $customerAddress->address()->save(Address::factory()->make());
        });
    }
}
