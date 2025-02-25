<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Http;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
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
            'customer_id' => Customer::factory(),
            'full_name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'address_type' => $this->faker->randomElement(['home', 'work', 'other']),
            'province' => $province['id'],  // Lưu ID thay vì tên
            'district' => $district['id'],  // Lưu ID thay vì tên
            'ward' => $ward['id'],          // Lưu ID thay vì tên
            'street_address' => $this->faker->address,
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
}
