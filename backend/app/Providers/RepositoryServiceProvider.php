<?php

namespace App\Providers;

use App\Models\ProductImage;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Repositories\Contracts\ContactInformationRepositoryInterface;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Repositories\Contracts\CultivationLogRepositoryInterface;
use App\Repositories\Contracts\CustomerProfileRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\FarmRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ParameterRepositoryInterface;
use App\Repositories\Contracts\PostCommentRepositoryInterface;
use App\Repositories\Contracts\ProductCatogoryRepositoryInterface;
use App\Repositories\Contracts\ProductImageRepositoryInterface;
use App\Repositories\Contracts\ProductQuantityPriceRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductUnitRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\SliderImageRepositoryInterface;
use App\Repositories\Contracts\SocialLinkRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\ActivityLogRepository;
use App\Repositories\Eloquent\CartItemRepository;
use App\Repositories\Eloquent\ContactInformationRepository;
use App\Repositories\Eloquent\ContactMessageRepository;
use App\Repositories\Eloquent\CultivationLogRepository;
use App\Repositories\Eloquent\CustomerProfileRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\FarmRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\OrderItemRepository;
use App\Repositories\Eloquent\OrderRedisRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ParameterRepository;
use App\Repositories\Eloquent\PostCommentRepository;
use App\Repositories\Eloquent\ProductCategoryRepository;
use App\Repositories\Eloquent\ProductImageRepository;
use App\Repositories\Eloquent\ProductQuantityPriceRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductUnitRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\SliderImageRepository;
use App\Repositories\Eloquent\SocialLinkRepository;
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
        $this->app->bind(ProductQuantityPriceRepositoryInterface::class, ProductQuantityPriceRepository::class);
        $this->app->bind(ProductImageRepositoryInterface::class, concrete: ProductImageRepository::class);
        $this->app->bind(CartItemRepositoryInterface::class, CartItemRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(SocialLinkRepositoryInterface::class, SocialLinkRepository::class);
        $this->app->bind(SliderImageRepositoryInterface::class, SliderImageRepository::class);
        $this->app->bind(ContactInformationRepositoryInterface::class, ContactInformationRepository::class);
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(ParameterRepositoryInterface::class, ParameterRepository::class);
        $this->app->bind(ContactMessageRepositoryInterface::class, ContactMessageRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
