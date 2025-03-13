<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'sometimes|email|unique:users,email|unique:customers,email,' . $this->id,
            'full_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20|unique:users,phone_number|unique:customers,phone_number,' . $this->id,
            'gender' => 'sometimes|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'avatar_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
