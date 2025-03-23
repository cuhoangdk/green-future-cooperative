<?php

namespace App\Http\Requests\Users;

use App\Helpers\LocationHelper;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:customers,email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:customers,phone_number|unique:users,phone_number',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|array',
            'address.province' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (!LocationHelper::isValidProvince($value)) {
                        $fail('Mã tỉnh không hợp lệ.');
                    }
                },
            ],
            'address.district' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $province = $this->input('address.province');
                    if (!$province || !LocationHelper::isValidDistrict($province, $value)) {
                        $fail('Mã quận/huyện không hợp lệ.');
                    }
                },
            ],
            'address.ward' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $district = $this->input('address.district');
                    if (!$district || !LocationHelper::isValidWard($district, $value)) {
                        $fail('Mã phường/xã không hợp lệ.');
                    }
                },
            ],
            'address.street_address' => 'nullable|string|max:500',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',            
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bio' => 'nullable|string|max:1000',
            'is_super_admin' => 'nullable|boolean',
            'is_banned' => 'nullable|boolean',
            'gender' => 'nullable|string|in:male,female,other',
        ];
    }
}
