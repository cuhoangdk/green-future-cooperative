<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Repositories\Contracts\UserAuthRepositoryInterface;
use App\Repositories\Eloquent\UserAuthRepository;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserAuthRepositoryInterface::class, UserAuthRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {       
        Passport::enablePasswordGrant();
        $this->registerPolicies();
        if (!app()->runningInConsole()) {
            // Thiết lập thời gian hết hạn token
            Passport::tokensExpireIn(now()->addHours(2));
            Passport::refreshTokensExpireIn(now()->addDays(30));
            Passport::personalAccessTokensExpireIn(now()->addHours(2));
        }
    }
}
