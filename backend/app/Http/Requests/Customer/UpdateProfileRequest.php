<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = auth('api_customers')->id();

        return [
            'email' => 'required|email|unique:users,email|unique:customers,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,phone_number|unique:customers,phone_number,' . $id,
            'gender' => 'required|string|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
