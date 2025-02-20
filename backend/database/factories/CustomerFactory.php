<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('12345678'), // Mật khẩu mặc định
            'avatar_url' => $this->faker->imageUrl(300, 300, 'people'),
            'full_name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'total_orders' => $this->faker->numberBetween(0, 50),
            'total_spending' => $this->faker->randomFloat(2, 100, 10000),
            'last_order_date' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'newsletter_subscribed' => $this->faker->boolean,
            'is_banned' => false,
            'verified_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
