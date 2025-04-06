<?php

use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'log.activity' => \App\Http\Middleware\LogActivity::class,
        ]);
        $middleware->append(CorsMiddleware::class);
        // $middleware->append(\Illuminate\Http\Middleware\TrustHosts::class); // Bảo vệ hostname        
        $middleware->append(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class); // Giới hạn kích thước POST
        $middleware->append(ForceJsonResponse::class); // Buộc trả về JSON
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TooManyRequestsHttpException $e) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => $e->getHeaders()['Retry-After'] ?? 60,
            ], 429);
        });
    })->create();
