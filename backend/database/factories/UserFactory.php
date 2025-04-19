<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $fullName = $this->faker->name();         
         

        return [
            'full_name' => $fullName,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('1234'), // Mật khẩu mặc định
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'avatar_url' => $this->faker->imageUrl(300, 300, 'people'),
            'bio' => $this->faker->sentence(),
            'is_super_admin' => $this->faker->boolean(10), // 10% cơ hội là super admin
            'is_banned' => $this->faker->boolean(5), // 5% cơ hội bị khóa
            'bank_account_number' => $this->faker->numerify('################'),
            'bank_name' => $this->faker->randomElement(['Vietcombank', 'Techcombank']),
            'last_login_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
        ];
    }

    /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (User $user) {            
            // Tự động tạo địa chỉ trong bảng addresses
            $user->address()->save(Address::factory()->make());
        });
    }
}
