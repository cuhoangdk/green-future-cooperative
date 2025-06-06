<?php

namespace App\Repositories\Eloquent;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendVerificationEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Jobs\CustomerSendResetPasswordEmail;
use App\Repositories\Contracts\CustomerAuthRepositoryInterface;

class CustomerAuthRepository implements CustomerAuthRepositoryInterface
{
    public function login(array $credentials)
    {
        $email = trim($credentials['email']);
        // Lấy thông tin khách hàng dựa trên email        
        $customer = Customer::where('email', $email)->first();

        // Kiểm tra khách hàng có tồn tại không
        if (!$customer) {            
            return response()->json(['message' => 'Bạn đã nhập sai thông tin email hoặc mật khẩu. Vui lòng kiểm tra lại.'], 401);
        }
        // Kiểm tra xem khách hàng có được verify chưa
        if (!$customer->verified_at) {
            return response()->json(['message' => 'Tài khoản của bạn chưa được kích hoạt.'], 403);
        }

        // Kiểm tra xem khách hàng có bị khóa không
        if ($customer->is_banned) {
            return response()->json(['message' => 'Tài khoản của bạn đã bị khoá.'], 403);
        }
        // Kiểm tra mật khẩu
        if (!Hash::check($credentials['password'], $customer->password)) {
            return response()->json(['message' => 'Bạn đã nhập sai thông tin email hoặc mật khẩu. Vui lòng kiểm tra lại.'], 401);
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
        $responseData = json_decode($response->getContent(), true);
        // Kiểm tra lỗi trong phản hồi
        if (isset($responseData['error'])) {
            return response()->json(['message' => $responseData['error_description']], $response->getStatusCode());
        }

        // Trả về thông tin token
        return response()->json($responseData);
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
            return response()->json(['message' => 'Email đã tồn tại trong hệ thống.'], 422);
        }
    
        if (Customer::where('phone_number', $data['phone_number'])->exists()) {
            return response()->json(['message' => 'Số điện thoại đã tồn tại trong hệ thống.'], 422);
        }
    
        
    
        // Tạo khách hàng
        $customer = Customer::create($data);

        // Tạo token xác minh và lưu vào bảng email_verification_tokens
        $token = Str::random(60); // Tạo token ngẫu nhiên
        DB::table('email_verification_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'email' => $customer->email,
                'token' => Hash::make($token), // Lưu token đã mã hóa
                'created_at' => now(),
            ]
        );
    
        // Gửi email xác minh
        dispatch(new SendVerificationEmail($customer, $token));
    
        return $customer;
    }
    


    public function sendResetLink(string $email): string
    {
        // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
        $customer = Customer::where('email', $email)->first();
    
        if (!$customer) {
            throw new \Exception('Email không tồn tại.', 404);
        }
    
        // Sử dụng broker để gửi reset link
        $status = Password::broker('customers')->sendResetLink(
            ['email' => $email],
            function ($user, $token) {
                // Gửi thông báo qua Notification
                dispatch(new CustomerSendResetPasswordEmail($user, $token));
            }
        );
    
        // Trả về trạng thái
        return $status === Password::RESET_LINK_SENT
            ? 'Gửi link tạo lại mật khẩu thành công. Vui lòng kiểm tra email'
            : 'Không thể gửi link tạo lại mật khẩu.';
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

        if ($status === Password::PASSWORD_RESET) {
            return 'Mật khẩu đã được đổi thành công.';
        }

        throw new \Exception('Token không hợp lệ.', 400);
    }


    public function changePassword(array $data)
    {
        $customer = Auth::user();

        if (!Hash::check($data['current_password'], $customer->password)) {
            throw new \Exception('Mật khẩu hiện tại không chính xác.');
        }

        $customer->update(['password' => $data['new_password']]);
    }
    public function verifyAccount(array $data)
    {
        $customer = Customer::where('email', $data['email'])->first();

        if (!$customer || $customer->verified_at) {
            return false;
        }

        // Kiểm tra token trong bảng email_verification_tokens
        $tokenRecord = DB::table('email_verification_tokens')
            ->where('email', $data['email'])
            ->first();

        if (!$tokenRecord || !Hash::check($data['token'], $tokenRecord->token)) {
            return false;
        }

        $createdAt = Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addHours(24) < now()) {
            return false;
        }
        $customer->update([
            'verified_at' => now(),            
        ]);

        return true;
    }

    public function resendVerificationToken(string $email)
    {
        // Tìm khách hàng theo email
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return response()->json(['message' => 'Email không tồn tại.'], 404);
        }

        if ($customer->verified_at) {
            return response()->json(['message' => 'Tài khoản đã được xác minh.'], 400);
        }

        // Tạo token mới và lưu vào bảng email_verification_tokens
        $token = Str::random(60);
        DB::table('email_verification_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'email' => $customer->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Gửi email xác minh với token mới
        dispatch(new SendVerificationEmail($customer, $token));

        return response()->json([
            'message' => 'Email xác minh đã được gửi lại. Vui lòng kiểm tra hộp thư của bạn.',
        ], 200);
    }
    public function refreshToken(string $refreshToken)
    {
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'client_id' => config('passport.client_id_customer'),
            'client_secret' => config('passport.client_secret_customer'),
            'refresh_token' => $refreshToken,
            'scope' => '*',
        ]);

        $response = app()->handle($tokenRequest);

        $responseData = json_decode($response->getContent(), true);

        if (isset($responseData['error'])) {
            return response()->json(['message' => $responseData['error_description']], $response->getStatusCode());
        }

        return response()->json($responseData);
    }

}
