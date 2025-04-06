<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Route;

class LogActivity
{
    protected $logService;

    public function __construct(ActivityLogService $logService)
    {
        $this->logService = $logService;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $response->getStatusCode() < 400) {
            $entityType = $this->getEntityTypeFromRequest($request);
            $action = $this->getActionFromRoute($request);

            if ($entityType) {
                $this->logService->logFromRequest($action, $entityType);
            }
        }

        return $response;
    }

    protected function getEntityTypeFromRequest(Request $request)
    {
        $segments = $request->segments();
        $prefix = $segments[1] ?? '';

        if (in_array($prefix, ['products', 'posts', 'customers'])) {
            $subPrefix = $segments[3] ?? '';
            if (in_array($subPrefix, ['cultivation-logs', 'quantity-prices', 'images', 'comments', 'addresses'])) {
                return $subPrefix;
            }
        }

        return str_replace('admin/', '', $prefix);
    }

    protected function getActionFromRoute(Request $request)
    {
        $route = $request->route();
        if (!$route) {
            return 'unknown';
        }

        $routeName = $route->getName();
        if (!$routeName) {
            return 'unknown';
        }

        // Tách phần cuối của route name để lấy hành động
        $parts = explode('.', $routeName);
        $action = end($parts);

        // Chuẩn hóa action nếu cần
        return match ($action) {
            'store' => 'create',
            'destroy' => 'delete',
            'forceDelete' => 'force-delete',
            'get-by-slug', 'get-by-product-code', 'index', 'show', 'search', 'trashed' => null, // Bỏ qua các hành động đọc
            default => $action,
        };
    }
}