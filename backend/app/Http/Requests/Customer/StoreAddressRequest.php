<?php

namespace App\Http\Requests\Customer;

use App\Helpers\LocationHelper;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address_type' => 'required|in:home,work,other',
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
            'street_address' => 'required|string|max:500',
            'is_default' => 'boolean',
        ];
    }
}
