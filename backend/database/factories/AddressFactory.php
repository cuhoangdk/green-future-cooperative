<?php
namespace Database\Factories;

use App\Models\Address;
use Http;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
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
            'province' => $province['id'],  
            'district' => $district['id'],  
            'ward' => $ward['id'],          
            'street_address' => $this->faker->address,
        ];
    }
}