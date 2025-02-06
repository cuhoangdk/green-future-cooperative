<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Đăng nhập và lấy token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $data = $this->authRepository->login($request->only('email', 'password'));

        if (!$data) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json($data);
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        $this->authRepository->logout();
        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Lấy thông tin người dùng hiện tại
     */
    public function user()
    {
        return response()->json(Auth::user());
    }
}
