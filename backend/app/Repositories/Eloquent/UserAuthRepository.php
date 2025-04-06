<?php

namespace App\Repositories\Eloquent;

use App\Jobs\SendResetPasswordEmail;
use App\Repositories\Contracts\UserAuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\RefreshToken;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Str;
use App\Notifications\ResetPasswordNotification;

class UserAuthRepository implements UserAuthRepositoryInterface
{
    /**
     * Đăng nhập và lấy Access Token + Refresh Token từ /oauth/token
     */
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        // Kiểm tra nếu tài khoản bị khóa (is_banned = true)
        if (!$user || $user->is_banned) {
            return response()->json(['message' => 'This account has been banned.'], 403);
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }
        // Cập nhật last_login_at
        $user->update(['last_login_at' => now()]);

        // Gọi trực tiếp OAuth để lấy token
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'password',
            'client_id'     => config('passport.client_id'),
            'client_secret' => config('passport.client_secret'),
            'username'      => $credentials['email'],
            'password'      => $credentials['password'],
            'scope'         => '*',
        ]);

        $response = app()->handle($tokenRequest);
        $responseData = json_decode($response->getContent(), true);

        // Kiểm tra lỗi trong phản hồi
        if (isset($responseData['error'])) {
            return response()->json(['message' => $responseData['error_description']], $response->getStatusCode());
        }

        return response()->json($responseData);
    }

    /**
     * Làm mới Access Token bằng Refresh Token từ /oauth/token
     */
    public function refreshToken(string $refreshToken)
    {
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'refresh_token',
            'client_id'     => config('passport.client_id'),
            'client_secret' => config('passport.client_secret'),
            'refresh_token' => $refreshToken,
            'scope'         => '*',
        ]);

        $response = app()->handle($tokenRequest);
        $responseData = json_decode($response->getContent(), true);

        if (isset($responseData['error'])) {
            return response()->json(['message' => $responseData['error_description']], $response->getStatusCode());
        }

        return response()->json($responseData);
    }

    /**
     * Đăng xuất (Thu hồi Access Token & Refresh Token, cập nhật last_login_at)
     */
    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Thu hồi Access Token
            $token = $user->token();
            $token->revoke();

            // Thu hồi Refresh Token liên kết
            RefreshToken::where('access_token_id', $token->id)->update(['revoked' => true]);
        }
    }

    /**
     * Gửi email reset mật khẩu.
     */
    public function sendResetLink(string $email)
    {
        $status = Password::broker()->sendResetLink(
            ['email' => $email],
            function ($user, $token) {
                dispatch(new SendResetPasswordEmail($user, $token));
            }
        );

        return $status === Password::RESET_LINK_SENT
            ? 'Reset link sent to your email.'
            : 'Unable to send reset link.';
    }

    /**
     * Đặt lại mật khẩu.
     */
    public function resetPassword(array $credentials)
    {
        $status = Password::reset(
            $credentials,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? 'Password reset successfully.'
            : 'Invalid token.';
    }
    /**
     * Đổi mật khẩu.
     */
    public function changePassword(int $userId, array $data)
    {
        $user = User::find($userId);

        if (!Hash::check($data['current_password'], $user->password)) {
            return 'Current password is incorrect.';
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return 'Password changed successfully.';
    }

    /**
     * Lấy thông tin profile.
     */
    public function getProfile(int $userId)
    {
        return User::find($userId);
    }

    /**
     * Cập nhật thông tin profile.
     */
    public function updateProfile(int $userId, array $data)
    {
        $user = User::find($userId);

        if ($user) {
            // Tách dữ liệu address nếu có
            $addressData = [
                'province' => $data['address']['province'] ?? null,
                'district' => $data['address']['district'] ?? null,
                'ward' => $data['address']['ward'] ?? null,
                'street_address' => $data['address']['street_address'] ?? null,
            ];
            unset($data['address']); // Loại bỏ address khỏi data chính

            // Loại bỏ các trường không cho phép cập nhật
            unset($data['is_super_admin'], $data['is_banned'], $data['password']);

            // Cập nhật thông tin user
            $user->update($data);

            // Cập nhật hoặc tạo address nếu có
            if ($addressData) {
                $user->address()->updateOrCreate(
                    ['addressable_id' => $user->id, 'addressable_type' => get_class($user)],
                    $addressData
                );
            }

            return $user;
        }

        return null;
    }
    /**
     * Xóa tài khoản người dùng (đánh dấu soft delete).
     */
    public function deleteProfile(int $userId)
    {
        $user = User::find($userId);

        if ($user) {
            $user->delete();
            return 'User profile deleted successfully.';
        }

        return 'Unable to delete user profile.';
    }
}
