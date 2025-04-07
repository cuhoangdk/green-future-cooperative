<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('customer_id')) {
            \Log::warning('Unauthenticated access attempt to checkout');
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }
        return $next($request);
    }
}
