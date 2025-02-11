<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
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

        // Tạo Access Token với quyền refresh
        $tokenResult = $member->createToken('access_token', ['*']);
        $accessToken = $tokenResult->accessToken;

        // Lấy token model
        $token = $tokenResult->token;
        $token->save();

        // Tạo Refresh Token thủ công
        $refreshToken = RefreshToken::create([
            'id'              => \Illuminate\Support\Str::uuid(), // Fix lỗi ID không có giá trị
            'access_token_id' => $token->id,
            'revoked'         => false,
            'expires_at'      => now()->addDays(30), // Đặt hạn 30 ngày
        ]);

        return [
            'user'          => $member,
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken->id,
            'token_type'    => 'Bearer',            
        ];
    }


    /**
     * Xử lý đăng xuất
     */
    public function logout()
    {
        if ($user = Auth::user()) {
            $token = $user->token();
            $token->revoke();

            // Thu hồi refresh token liên kết với access token này
            RefreshToken::where('access_token_id', $token->id)->update(['revoked' => true]);
        }
    }


    /**
     * Làm mới access token bằng refresh token
     */
    public function refreshToken($refreshTokenId)
    {
        // Kiểm tra Refresh Token hợp lệ
        $refreshToken = RefreshToken::where('id', $refreshTokenId)
            ->where('revoked', false)
            ->first();

        if (!$refreshToken) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        // Lấy access token cũ
        $oldAccessToken = Token::find($refreshToken->access_token_id);
        if (!$oldAccessToken || $oldAccessToken->revoked) {
            return response()->json(['error' => 'Access token expired or revoked'], 401);
        }

        // Lấy thành viên từ Access Token cũ
        $member = CooperativeMember::find($oldAccessToken->user_id);
        if (!$member) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Tạo Access Token mới
        $tokenResult = $member->createToken('authToken', ['*']);
        $newAccessToken = $tokenResult->accessToken;
        
        // Hủy Access Token & Refresh Token cũ
        $oldAccessToken->revoke();
        $refreshToken->update(['revoked' => true]);

        // Tạo Refresh Token mới
        $newRefreshToken = RefreshToken::create([
            'id'              => \Illuminate\Support\Str::uuid(),
            'access_token_id' => $tokenResult->token->id,
            'revoked'         => false,
            'expires_at'      => now()->addDays(30),
        ]);

        return [
            'access_token'  => $newAccessToken,
            'refresh_token' => $newRefreshToken->id,
            'token_type'    => 'Bearer',            
        ];
    }
}
