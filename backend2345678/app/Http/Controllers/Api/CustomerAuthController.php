<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\Customer\ResetPasswordCustomerRequest;
use App\Http\Requests\Auth\Customer\ForgotPasswordCustomerRequest;
use App\Models\User;
use App\Repositories\Contracts\CustomerAuthRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Eloquent\NotificationRepository;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    protected $authRepository;

    public function __construct(CustomerAuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Đăng nhập.
     * 
     * @param LoginRequest $request - Chứa `email`, `password`.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - Danh sách thông tin user dạng JSON.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $tokenData = $this->authRepository->login($credentials);

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json($tokenData);
    }

    /**
     * Đăng xuất.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authRepository->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Đăng ký tài khoản.
     * 
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $customer = $this->authRepository->register($data); // $customer là instance của Customer

            // Tạo thông báo cho super admins
            $notificationRepo = app(NotificationRepositoryInterface::class);
            $superAdmins = User::where('is_super_admin', true)->get();
            foreach ($superAdmins as $superAdmin) {
                $notificationRepo->create([
                    'user_type' => 'member',
                    'user_id' => $superAdmin->id,
                    'title' => "Khách hàng mới đăng ký: {$customer->email}",
                    'type' => 'new_customer',                    
                ]);
            }

            return response()->json([
                'message' => 'Customer registered successfully. Please verify your email.',
                'customer' => $customer,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }
    /**
     * Xác minh tài khoản khách hàng.
     *
     * @param VerifyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyAccount(VerifyRequest $request)    {
        

        $verified = $this->authRepository->verifyAccount($request->only('email', 'token'));

        if (!$verified) {
            return response()->json(['message' => 'Invalid token or email'], 400);
        }

        return response()->json(['message' => 'Account verified successfully'], 200);
    }


    /**
     * Quên mật khẩu.
     * 
     * @param ForgotPasswordCustomerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordCustomerRequest $request)
    {
        try {
            $message = $this->authRepository->sendResetLink($request->email);
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
}


    /**
     * Đặt lại mật khẩu.
     * 
     * @param ResetPasswordCustomerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordCustomerRequest $request)
    {
        try {
            $message = $this->authRepository->resetPassword($request->only('email', 'token', 'password', 'password_confirmation'));
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    /**
     * Đổi mật khẩu.
     * 
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->authRepository->changePassword($request->only('current_password', 'new_password', 'new_password_confirmation'));

        return response()->json(['message' => 'Password changed successfully'], 200);
    }    
    /**
     * Làm mới Access Token
     *
     * @param RefreshTokenRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(RefreshTokenRequest $request)
    {
        $tokenData = $this->authRepository->refreshToken($request->refresh_token);

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        return response()->json($tokenData);
    }
}