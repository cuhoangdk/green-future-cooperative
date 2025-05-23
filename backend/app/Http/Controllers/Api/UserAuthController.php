<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\User\ResetPasswordUserRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\User\UpdateProfileUserRequest;
use App\Http\Requests\Auth\User\ForgetPasswordUserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserAuthRepositoryInterface;
use App\Services\UploadFileService;
use Illuminate\Support\Facades\Auth;
class UserAuthController extends Controller
{
    protected $authRepository;
    protected $uploadService;

    public function __construct(UserAuthRepositoryInterface $authRepository, UploadFileService $uploadService)
    {
        $this->authRepository = $authRepository;
        $this->uploadService = $uploadService;
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
            return response()->json(['message' => 'Bạn đã nhập sai email hoặc mật khẩu'], 401);
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
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authRepository->logout();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }    
   
    /**
     * Gửi email reset mật khẩu.
     *
     * @param ForgetPasswordUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(ForgetPasswordUserRequest $request)
    {
        $message = $this->authRepository->sendResetLink($request->email);

        if ($message === 'Gửi link tạo lại mật khẩu thành công. Vui lòng kiểm tra email.') {
            return response()->json(['message' => $message], 200);
        }

        return response()->json(['message' => $message], 400);
    }

    /**
     * Đặt lại mật khẩu.
     *
     * @param ResetPasswordUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordUserRequest $request)
    {
        $message = $this->authRepository->resetPassword($request->only('email', 'password', 'password_confirmation', 'token'));

        if ($message === 'Mật khẩu đã được đặt lại thành công.') {
            return response()->json(['message' => $message], 200);
        }

        return response()->json(['message' => $message], 400);
    }
     /**
     * Đổi mật khẩu.
     * 
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $message = $this->authRepository->changePassword(Auth::id(), $request->only('current_password', 'new_password', 'new_password_confirmation'));

        if ($message === 'Đổi mật khẩu thành công.') {
            return response()->json(['message' => $message], 200);
        }

        return response()->json(['message' => $message], 400);
    }

    /**
     * Lấy thông tin profile người dùng hiện tại.
     * 
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        $user = $this->authRepository->getProfile(Auth::id());
        if ($user) {
            return new UserResource($user->load('address'));
        }
    
        return response()->json(['message' => 'User not found.'], 404);
    }

    /**
     * Cập nhật thông tin profile người dùng.
     * 
     * @param UpdateProfileUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfileUserRequest $request)
    {
        $validated = $request->validated();
        $user = auth('api_users')->user();
        if ($request->hasFile('avatar_url')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            $this->uploadService->deleteImage($user->avatar_url);

            // Upload ảnh mới
            $validated['avatar_url'] = $this->uploadService->uploadImage($request->file('avatar_url'), 'customers');
        }
        $user = $this->authRepository->updateProfile(Auth::id(), $validated);
        
        if ($user) {
            return response()->json(['message' => 'Profile updated successfully', 'data' => new UserResource($user->load('address'))]);
        }

        return response()->json(['message' => 'Unable to update profile.'], 400);
    }
    /**
     * Xóa tài khoản người dùng.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount()
    {
        $message = $this->authRepository->deleteProfile(Auth::id());

        if ($message === 'Xoá tài khoản thành công.') {
            return response()->json(['message' => $message], 200);
        }

        return response()->json(['message' => $message], 400);
    }
}
