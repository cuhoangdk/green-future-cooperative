<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Đăng ký middleware toàn cục
        // $middleware->append(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $middleware->prepend(\Illuminate\Foundation\Http\Middleware\TrimStrings::class);

        // Đăng ký middleware nhóm (thay thế kernel.php)
        $middleware->group('api', [
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
            \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
