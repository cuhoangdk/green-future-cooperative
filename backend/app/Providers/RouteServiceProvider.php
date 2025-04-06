<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Các không gian tên cho các route trong ứng dụng.
     *
     * @var string
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Đăng ký các route cho ứng dụng.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Đăng ký các route API cho ứng dụng.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Đăng ký các route web cho ứng dụng.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
    public function boot()
    {
        RateLimiter::for('emailLogin', function (Request $request) {
            $email = Str::lower($request->input('email')); // Lấy email từ request và chuyển về chữ thường
            if (!$email) {
                // Nếu không có email trong request, không áp dụng giới hạn này
                return \Illuminate\Cache\RateLimiting\Limit::none();
            }
            
            $key = 'login:email:' . $email; // Key duy nhất dựa trên email
            return new \Illuminate\Cache\RateLimiting\Limit(
                $key, // Key cho rate limiter
                5,    // Giới hạn 5 lần
                1     // Trong 1 phút
            );
        });
    }
}
