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
}
