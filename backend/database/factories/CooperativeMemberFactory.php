<?php

namespace Database\Factories;

use App\Models\CooperativeMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CooperativeMemberFactory extends Factory
{
    protected $model = CooperativeMember::class;

    public function definition(): array
    {
        $username = $this->faker->unique()->userName;
        return [
            'username' => $username,
            'slug' => Str::slug($username),
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('1234'), // Mật khẩu mặc định
            'full_name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'farm_location' => $this->faker->city,
            'farm_size' => $this->faker->randomFloat(1, 1, 50), // Diện tích nông trại ngẫu nhiên từ 1 - 50 ha
            'bank_account_number' => $this->faker->bankAccountNumber,
            'bank_name' => $this->faker->randomElement(['Vietcombank', 'BIDV', 'Techcombank', 'MB Bank']),
            'avatar_url' => $this->faker->imageUrl(200, 200, 'people'),
            'bio' => $this->faker->sentence,
            'is_active' => $this->faker->boolean(80), // 80% là active
            'verified_at' => now(),
            'joined_at' => now(),
        ];
    }
}
