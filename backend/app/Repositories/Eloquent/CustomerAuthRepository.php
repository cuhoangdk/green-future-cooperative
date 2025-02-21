<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerAuthRepositoryInterface;
use App\Notifications\VerifyCustomerAccount;
use App\Notifications\CustomerResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerAuthRepository implements CustomerAuthRepositoryInterface
{
    public function login(array $credentials)
    {
        // Lấy thông tin khách hàng dựa trên email
        $customer = Customer::where('email', $credentials['email'])->first();

        // Kiểm tra khách hàng có tồn tại không
        if (!$customer) {
            return response()->json(['message' => 'Invalid email'], 401);
        }
        // Kiểm tra xem khách hàng có được verify chưa
        if (!$customer->verified_at) {
            return response()->json(['message' => 'Your account has not been verified.'], 403);
        }

        // Kiểm tra xem khách hàng có bị khóa không
        if ($customer->is_banned) {
            return response()->json(['message' => 'Your account has been banned.'], 403);
        }
        // Kiểm tra mật khẩu
        if (!Hash::check($credentials['password'], $customer->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        // Nếu thông tin chính xác, gọi /oauth/token để lấy token
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('passport.client_id_customer'),
            'client_secret' => config('passport.client_secret_customer'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => '*',
        ]);

        $response = app()->handle($tokenRequest);

        // Kiểm tra xem token có được trả về không
        if ($response->getStatusCode() !== 200) {
            return response()->json(['message' => 'Unable to login, please try again later.'], 500);
        }

        // Trả về thông tin token
        return json_decode($response->getContent(), true);
    }


    public function logout()
    {
        if (Auth::check()) {
            $token = Auth::user()->token();
            $token->revoke();
        }
    }

    public function register(array $data)
    {
        // Kiểm tra xem email hoặc số điện thoại đã tồn tại chưa
        if (Customer::where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'Email already exists.'], 422);
        }
    
        if (Customer::where('phone_number', $data['phone_number'])->exists()) {
            return response()->json(['message' => 'Phone number already exists.'], 422);
        }
    
        // Tạo token xác minh
        $data['remember_token'] = Str::random(60);
    
        // Tạo khách hàng
        $customer = Customer::create($data);
    
        // Gửi email xác minh
        $customer->notify(new VerifyCustomerAccount($customer->remember_token));
    
        return response()->json([
            'message' => 'Customer registered successfully. Please verify your email.',
            'customer' => $customer,
        ], 201);
    }
    


    public function sendResetLink(string $email): string
    {
        // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
        $customer = Customer::where('email', $email)->first();
    
        if (!$customer) {
            throw new \Exception('Email not found.', 404);
        }
    
        // Sử dụng broker để gửi reset link
        $status = Password::broker('customers')->sendResetLink(
            ['email' => $email],
            function ($user, $token) {
                // Gửi thông báo qua Notification
                $user->notify(new CustomerResetPasswordNotification($token));
            }
        );
    
        // Trả về trạng thái
        return $status === Password::RESET_LINK_SENT
            ? 'Reset link sent to your email.'
            : 'Unable to send reset link.';
    }
    

    public function resetPassword(array $credentials): string
    {
        $status = Password::broker('customers')->reset(
            $credentials,
            function ($customer, $password) {
                $customer->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? 'Password reset successfully.'
            : 'Invalid token.';
    }


    public function changePassword(array $data)
    {
        $customer = Auth::user();

        if (!Hash::check($data['current_password'], $customer->password)) {
            throw new \Exception('Current password is incorrect.');
        }

        $customer->update(['password' => $data['new_password']]);
    }
    public function verifyAccount(array $data)
    {
        $customer = Customer::where('email', $data['email'])->first();

        if (!$customer || $customer->verified_at || $customer->remember_token !== $data['token']) {
            return false;
        }

        $customer->update([
            'verified_at' => now(),
            'remember_token' => null,
        ]);

        return true;
    }
    public function refreshToken(string $refreshToken)
    {
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'client_id' => config('passport.client_id_customer'),
            'client_secret' => config('passport.client_customer'),
            'refresh_token' => $refreshToken,
            'scope' => '*',
        ]);

        $response = app()->handle($tokenRequest);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        return json_decode($response->getContent(), true);
    }

}
