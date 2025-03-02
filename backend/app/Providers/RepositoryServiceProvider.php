<?php

namespace App\Providers;

use App\Repositories\Contracts\CultivationLogRepositoryInterface;
use App\Repositories\Contracts\CustomerProfileRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\FarmRepositoryInterface;
use App\Repositories\Contracts\PostCommentRepositoryInterface;
use App\Repositories\Contracts\ProductCatogoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductUnitRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\CultivationLogRepository;
use App\Repositories\Eloquent\CustomerProfileRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\FarmRepository;
use App\Repositories\Eloquent\PostCommentRepository;
use App\Repositories\Eloquent\ProductCategoryRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductUnitRepository;
use App\Repositories\Eloquent\RoleRepository;
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
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(FarmRepositoryInterface::class, FarmRepository::class);
        $this->app->bind(ProductUnitRepositoryInterface::class, ProductUnitRepository::class);
        $this->app->bind(ProductCatogoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CultivationLogRepositoryInterface::class, CultivationLogRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
