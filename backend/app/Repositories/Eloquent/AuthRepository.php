<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CooperativeMember;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Xử lý đăng nhập và tạo token
     */
    public function login(array $credentials)
    {
        // Tìm thành viên theo email
        $member = CooperativeMember::where('email', $credentials['email'])->first();

        // Kiểm tra mật khẩu
        if (!$member || !Hash::check($credentials['password'], $member->password)) {
            return null;
        }

        // Tạo token cho CooperativeMember
        return [
            'user'         => $member,
            'access_token' => $member->createToken('authToken', ['*'])->accessToken,
            'token_type'   => 'Bearer',
        ];
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout()
    {
        if (Auth::user()) {
            Auth::user()->token()->revoke();
        }
    }
}
