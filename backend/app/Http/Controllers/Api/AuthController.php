<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
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
     * 
     * @param LoginRequest $request - Chứa `email`, `password`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách thông tin user dạng JSON.
     */
    public function login(LoginRequest $request)
    {        
        $tokenData = $this->authRepository->login($request->only('email', 'password'));

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json($tokenData);
    }

    /**
     * Làm mới Access Token
     * 
     * @param RefreshTokenRequest $request - Chứa `refresh_token`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách thông tin refresh token mới và access token mới dạng JSON.
     */
    public function refreshToken(RefreshTokenRequest $request)
    {
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
