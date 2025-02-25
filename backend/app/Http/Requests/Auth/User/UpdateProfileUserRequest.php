<?php

namespace App\Http\Requests\Auth\User;

use App\Helpers\LocationHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'full_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'date_of_birth' => 'required|date|before:today',
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
            'street_address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            
        ];
    }
}
