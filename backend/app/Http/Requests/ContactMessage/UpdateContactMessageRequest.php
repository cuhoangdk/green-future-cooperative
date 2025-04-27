<?php

namespace App\Http\Requests\ContactMessage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Điều chỉnh theo logic phân quyền nếu cần
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:15',
            'gender' => 'sometimes|in:male,female,other',
            'message' => 'sometimes|string|max:5000',
        ];
    }
}