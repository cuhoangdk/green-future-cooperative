<?php

namespace App\Http\Requests\Users;

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
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'date_of_birth' => 'required|date|before:today',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'street_address' => 'required|string',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bio' => 'nullable|string|max:1000',
            'is_super_admin' => 'nullable|boolean',
            'is_banned' => 'nullable|boolean',
            'gender' => 'required|string|in:male,female,other',
            
        ];
    }
}
