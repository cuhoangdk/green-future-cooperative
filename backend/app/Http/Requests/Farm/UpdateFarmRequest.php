<?php
namespace App\Http\Requests\Farm;

use App\Helpers\LocationHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'province' => ['required', 'string', function ($attribute, $value, $fail) {
            if (!LocationHelper::isValidProvince($value)) {
                $fail('Mã tỉnh không hợp lệ.');
            }
            }],
            'district' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!LocationHelper::isValidDistrict($this->province, $value)) {
                    $fail('Mã quận/huyện không hợp lệ.');
                }
            }],
            'ward' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!LocationHelper::isValidWard($this->district, $value)) {
                    $fail('Mã phường/xã không hợp lệ.');
                }
            }],
            'street_address' => 'nullable|string',
            'description' => 'nullable|string',
            'farm_size' => 'nullable|numeric|min:0',
            'soil_type' => 'nullable|string|max:255',
            'irrigation_method' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }
}
