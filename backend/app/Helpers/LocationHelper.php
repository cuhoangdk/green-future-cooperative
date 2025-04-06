<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class LocationHelper
{
    public static function isValidProvince($provinceId)
    {
        $url = "https://esgoo.net/api-tinhthanh/1/0.htm";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            return collect($data['data'])->contains('id', $provinceId);
        }
        return false;
    }

    public static function isValidDistrict($provinceId, $districtId)
    {
        $url = "https://esgoo.net/api-tinhthanh/2/{$provinceId}.htm";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            return collect($data['data'])->contains('id', $districtId);
        }
        return false;
    }

    public static function isValidWard($districtId, $wardId)
    {
        $url = "https://esgoo.net/api-tinhthanh/3/{$districtId}.htm";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            return collect($data['data'])->contains('id', $wardId);
        }
        return false;
    }


    public static function getLocationName($url)
    {
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            // Kiểm tra dữ liệu hợp lệ
            if (is_array($data) && isset($data['data']) && !empty($data['data']) && $data['error'] == 0) {
                // Truy cập trực tiếp $data['data'] thay vì $data['data'][0]
                return $data['data']['full_name'] ?? $data['data']['name'] ?? 'Unknown';
            }

            \Log::warning("Invalid API response from $url: " . $response);
            return 'Unknown';
        } catch (\Exception $e) {
            \Log::error("Failed to fetch location from $url: " . $e->getMessage());
            return 'Unknown';
        }
    }
}
