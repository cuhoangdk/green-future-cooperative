<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AuthRepositoryInterface;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Đăng nhập & lấy Access Token + Refresh Token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $tokenData = $this->authRepository->login($request->only('email', 'password'));

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json($tokenData);
    }

    /**
     * Làm mới Access Token
     */
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $tokenData = $this->authRepository->refreshToken($request->refresh_token);

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        return response()->json($tokenData);
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        $this->authRepository->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
