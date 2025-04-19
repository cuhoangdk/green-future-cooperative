<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UploadFileService;
use Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UploadFileService::class, function ($app) {
            return new UploadFileService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
