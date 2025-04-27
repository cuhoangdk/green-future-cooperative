<?php

namespace App\Http\Requests\ContactMessage;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Điều chỉnh theo logic phân quyền nếu cần
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female,other',
            'message' => 'required|string|max:5000',
        ];
    }
}