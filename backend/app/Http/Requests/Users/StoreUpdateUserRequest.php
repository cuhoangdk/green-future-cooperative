<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Điều chỉnh logic nếu cần giới hạn quyền
    }

    public function rules(): array
    {
        $id = $this->route('id'); // Lấy `id` từ route (nếu có)

        return [
            'email' => 'required|email|unique:users,email,' . $id, // Kiểm tra trùng email trừ user đang cập nhật
            'password' => $this->isMethod('post') ? 'required|min:6' : 'nullable|min:6', // Bắt buộc với store, tùy chọn với update
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,phone_number,' . $id,
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
