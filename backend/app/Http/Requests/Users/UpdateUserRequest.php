<?php

namespace App\Http\Requests\Users;

use App\Helpers\LocationHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'email' => 'sometimes|email|unique:users,email,' . $id. '|unique:customers,email',            
            'full_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20|unique:users,phone_number,' . $id,
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
            'address.street_address' => 'sometimes|string|max:500',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bio' => 'nullable|string|max:1000',
            'is_super_admin' => 'nullable|boolean',
            'is_banned' => 'nullable|boolean',
            'gender' => 'sometimes|string|in:male,female,other',
            
        ];
    }
}
