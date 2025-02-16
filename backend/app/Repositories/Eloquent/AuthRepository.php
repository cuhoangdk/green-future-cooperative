<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\RefreshToken;
use Illuminate\Http\Request;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Đăng nhập và lấy Access Token + Refresh Token từ /oauth/token
     */
    public function login(array $credentials)
    {
        $member = User::where('email', $credentials['email'])->first();

        if (!$member || !Hash::check($credentials['password'], $member->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Gọi trực tiếp OAuth mà không dùng HTTP request
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'password',
            'client_id'     => config('passport.client_id'),
            'client_secret' => config('passport.client_secret'),
            'username'      => $credentials['email'],
            'password'      => $credentials['password'],
            'scope'         => '*',
        ]);

        $response = app()->handle($tokenRequest);
        return response()->json(json_decode($response->getContent(), true));
    }

    /**
     * Làm mới Access Token bằng Refresh Token từ /oauth/token
     */
    public function refreshToken(string $refreshToken)
    {
        // Gọi trực tiếp Laravel Passport để lấy token mới
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'refresh_token',
            'client_id'     => config('passport.client_id'),
            'client_secret' => config('passport.client_secret'),
            'refresh_token' => $refreshToken,
            'scope'         => '*',
        ]);

        // Xử lý request trực tiếp trong Laravel
        $response = app()->handle($tokenRequest);

        // Trả về kết quả
        return response()->json(json_decode($response->getContent(), true));
    }

    /**
     * Đăng xuất (Thu hồi Access Token & Refresh Token)
     */
    public function logout()
    {
        if (Auth::check()) {
            $token = Auth::user()->token();
            $token->revoke();

            // Thu hồi refresh token liên kết với access token này
            RefreshToken::where('access_token_id', $token->id)->update(['revoked' => true]);
        }
    }
}
