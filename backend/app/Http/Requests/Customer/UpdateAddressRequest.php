<?php

namespace App\Http\Requests\Customer;

use App\Helpers\LocationHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20',
            'address_type' => 'sometimes|in:home,work,other',
            'address' => 'sometimes|array',
            'address.province' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    if (!LocationHelper::isValidProvince($value)) {
                        $fail('Mã tỉnh không hợp lệ.');
                    }
                },
            ],
            'address.district' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    $province = $this->input('address.province');
                    if (!$province || !LocationHelper::isValidDistrict($province, $value)) {
                        $fail('Mã quận/huyện không hợp lệ.');
                    }
                },
            ],
            'address.ward' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    $district = $this->input('address.district');
                    if (!$district || !LocationHelper::isValidWard($district, $value)) {
                        $fail('Mã phường/xã không hợp lệ.');
                    }
                },
            ],
            'address.street_address' => 'sometimes|string|max:500',
            'is_default' => 'boolean',
        ];
    }
}
