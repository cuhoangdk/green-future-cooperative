<?php

namespace App\Http\Requests\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'full_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'date_of_birth' => 'required|date|before:today',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|url|max:255',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
        ];
    }
}
