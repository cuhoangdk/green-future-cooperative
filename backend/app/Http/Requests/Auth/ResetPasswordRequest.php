<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Xác định người dùng có được phép thực hiện request hay không.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Cho phép tất cả các yêu cầu
    }

    /**
     * Quy tắc xác thực cho request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required',
        ];
    }

    /**
     * Thông báo lỗi tùy chỉnh.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'token.required' => 'Token không được để trống.',
        ];
    }
}
