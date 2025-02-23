<?php

namespace App\Http\Requests\Customer;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:customers,email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'gender' => 'required|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
