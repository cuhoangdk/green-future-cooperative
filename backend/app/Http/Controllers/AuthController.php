<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if ($customer && \Hash::check($request->password, $customer->password)) {
            // Lưu thông tin khách hàng vào session
            Session::put('customer_id', $customer->id);
            Session::put('customer_name', $customer->full_name);
            \Log::info('Customer logged in: ' . $customer->id);
            return redirect()->route('vnpay.checkout');
        }

        \Log::warning('Login failed for email: ' . $request->email);
        return back()->with('error', 'Email hoặc mật khẩu không đúng');
    }

    public function logout()
    {
        Session::forget('customer_id');
        Session::forget('customer_name');
        \Log::info('Customer logged out');
        return redirect()->route('login');
    }
}