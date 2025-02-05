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
    public function login(Request $request)
    {
        $data = $this->authRepository->login($request->only('email', 'password'));

        if (!$data) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json($data);
    }

    public function logout()
    {
        $this->authRepository->logout();
        return response()->json(['message' => 'Logged out']);
    }

    public function refreshToken()
    {
        return response()->json([
            'access_token' => $this->authRepository->refreshToken()
        ]);
    }
}
