<?php

namespace App\Http\Requests\Auth\User;

use App\Helpers\LocationHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileUserRequest extends FormRequest
{
    public function rules()
    {
        $id = auth('api_users')->id();
        return [
            'full_name' => 'sometimes|string|max:255',
            'phone_number' => [
                'sometimes',
                'string',
                'max:15',
                // Kiểm tra unique trong bảng users và customers, ngoại trừ user hiện tại
                Rule::unique('users', 'phone_number')->ignore($id),
                Rule::unique('customers', 'phone_number'), // Giả sử bảng customers có cột phone_number
            ],          
            'date_of_birth' => 'sometimes|date|before:today',
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
            'address.street_address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'gender' => 'sometimes|string|in:male,female,other',
        ];
    }
}
