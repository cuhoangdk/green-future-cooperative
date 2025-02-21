<?php

namespace App\Providers;

use App\Repositories\Contracts\CustomerProfileRepositoryInterface;
use App\Repositories\Contracts\PostCommentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\CustomerProfileRepository;
use App\Repositories\Eloquent\PostCommentRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;
use App\Repositories\Eloquent\PostRepository;
use App\Repositories\Eloquent\PostCategoryRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Bind interface với implementation
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PostCategoryRepositoryInterface::class, PostCategoryRepository::class);  
        $this->app->bind(PostCommentRepositoryInterface::class, PostCommentRepository::class);  
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);   
        $this->app->bind(CustomerProfileRepositoryInterface::class, CustomerProfileRepository::class);  
            
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
