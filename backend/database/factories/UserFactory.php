<?php

namespace Database\Factories;

use App\Models\User;
use Http;
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
         // Lấy danh sách tỉnh
         $provinces = json_decode(Http::get("https://esgoo.net/api-tinhthanh/1/0.htm")->body(), true)['data'] ?? [];
         $province = collect($provinces)->random();
 
         // Lấy danh sách quận/huyện
         $districts = json_decode(Http::get("https://esgoo.net/api-tinhthanh/2/{$province['id']}.htm")->body(), true)['data'] ?? [];
         $district = collect($districts)->random();
 
         // Lấy danh sách phường/xã
         $wards = json_decode(Http::get("https://esgoo.net/api-tinhthanh/3/{$district['id']}.htm")->body(), true)['data'] ?? [];
         $ward = collect($wards)->random();
         

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
            'province' => $province['id'],  // Lưu ID thay vì tên
            'district' => $district['id'],  // Lưu ID thay vì tên
            'ward' => $ward['id'],          // Lưu ID thay vì tên
            'street_address' => $this->faker->address(),
            'usercode' => null, // Sẽ được tự động tạo
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
            // Tạo usercode tự động
            $user->usercode = $user->generateUserCode($user->full_name, User::class);
        })->afterCreating(function (User $user) {
            // Gán lại usercode nếu cần sau khi tạo
            if (empty($user->usercode)) {
                $user->update(['usercode' => $user->generateUserCode($user->full_name, User::class)]);
            }
        });
    }
}
