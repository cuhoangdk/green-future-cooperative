<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $routeName = $request->route()->getName(); // Lấy route name hiện tại

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Super Admin có quyền truy cập tất cả
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Kiểm tra xem user có permission tương ứng với route name không
        if (!$user->hasPermission($routeName)) {
            return response()->json(['message' => 'Forbidden: You do not have permission to access this route'], 403);
        }

        return $next($request);
    }
}
