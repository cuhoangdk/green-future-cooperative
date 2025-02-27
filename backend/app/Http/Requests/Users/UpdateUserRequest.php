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
            'email' => 'required|email|unique:users,email,' . $id. '|unique:customers,email',
            'password' => 'nullable|min:6|confirmed',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,phone_number,' . $id,
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|array',
            'address.province' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!LocationHelper::isValidProvince($value)) {
                        $fail('Mã tỉnh không hợp lệ.');
                    }
                },
            ],
            'address.district' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $province = $this->input('address.province');
                    if (!$province || !LocationHelper::isValidDistrict($province, $value)) {
                        $fail('Mã quận/huyện không hợp lệ.');
                    }
                },
            ],
            'address.ward' => [
                'required',
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
            'street_address' => 'required|string',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bio' => 'nullable|string|max:1000',
            'is_super_admin' => 'nullable|boolean',
            'is_banned' => 'nullable|boolean',
            'gender' => 'required|string|in:male,female,other',
            
        ];
    }
}
