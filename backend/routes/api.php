<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AdminOrderController;
use App\Http\Controllers\Api\ContactInformationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SliderImageController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\CultivationLogController;
use App\Http\Controllers\Api\FarmController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ProductQuantityPriceController;
use App\Http\Controllers\Api\ProductUnitController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostCategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\PostCommentController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerAddressController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;



Route::middleware('log.activity')->group(function () {
    Route::middleware(['auth:api_users,api_customers'])->group(function () {
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
            Route::put('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
            Route::get('/{id}', [NotificationController::class, 'show'])->name('notifications.show');
            Route::put('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');        
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        });
    });
    Route::prefix('activity-logs')->middleware(['auth:api_users', 'permission'])->group(function () {   
        Route::get('/', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

    Route::prefix('slider-images')->group(function () {
        Route::get('/', [SliderImageController::class, 'index'])->name('slider-images.index');
        Route::get('/{id}', [SliderImageController::class, 'show'])->name('slider-images.show');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [SliderImageController::class, 'store'])->name('slider-images.store');
            Route::put('/{id}', [SliderImageController::class, 'update'])->name('slider-images.update');
            Route::delete('/{id}', [SliderImageController::class, 'destroy'])->name('slider-images.destroy');
        });
    });

    Route::prefix('contact-informations')->group(function () {
        Route::get('/', [ContactInformationController::class, 'index'])->name('contact-informations.index');
        Route::get('/{id}', [ContactInformationController::class, 'show'])->name('contact-informations.show');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [ContactInformationController::class, 'store'])->name('contact-informations.store');
            Route::put('/{id}', [ContactInformationController::class, 'update'])->name('contact-informations.update');
            Route::delete('/{id}', [ContactInformationController::class, 'destroy'])->name('contact-informations.destroy');
        });
    });

    Route::prefix('social-links')->group(function () {
        Route::get('/', [SocialLinkController::class, 'index'])->name('social-links.index');    
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [SocialLinkController::class, 'store'])->name('social-links.store');       
            Route::put('/{id}', [SocialLinkController::class, 'update'])->name('social-links.update');
            Route::delete('/{id}', [SocialLinkController::class, 'destroy'])->name('social-links.destroy');
        });   
        Route::get('/{id}', [SocialLinkController::class, 'show'])->name('social-links.show'); 
    });

    Route::prefix('orders')->middleware(['auth:api_customers'])->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    });

    Route::prefix('admin/orders')->middleware(['auth:api_users', 'permission'])->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::post('/', [AdminOrderController::class, 'store'])->name('admin.orders.store');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::put('/{id}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
        Route::post('/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
    });

    Route::prefix('cart')->middleware(['auth:api_customers'])->group(function () {
        Route::get('/', [CartItemController::class, 'index'])->name('cart.index');
        Route::post('/', [CartItemController::class, 'store'])->name('cart.store');
        Route::get('/{id}', [CartItemController::class, 'show'])->name('cart.show');
        Route::put('/{id}', [CartItemController::class, 'update'])->name('cart.update');
        Route::delete('/{id}', [CartItemController::class, 'destroy'])->name('cart.destroy');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/search', [ProductController::class, 'searchByName'])->name('products.search');
        Route::get('/search-with-filters', [ProductController::class, 'filter'])->name('products.search-with-filter');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [ProductController::class, 'store'])->name('products.store');
            Route::get('/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
        });        
        Route::prefix('{product_id}/cultivation-logs')->group(function () {
            Route::get('/', [CultivationLogController::class, 'index'])->name('cultivation-logs.index');
            Route::middleware(['auth:api_users', 'permission'])->group(function () {
                Route::post('/', [CultivationLogController::class, 'store'])->name('cultivation-logs.store');
                Route::get('/{id}', [CultivationLogController::class, 'show'])->name('cultivation-logs.show');
                Route::put('/{id}', [CultivationLogController::class, 'update'])->name('cultivation-logs.update');
                Route::delete('/{id}', [CultivationLogController::class, 'destroy'])->name('cultivation-logs.destroy');
            });
        });
        Route::prefix('{product_id}/quantity-prices')->group(function () {
            Route::get('/', [ProductQuantityPriceController::class, 'index'])->name('quantity-prices.index');
            Route::middleware(['auth:api_users', 'permission'])->group(function () {
                Route::post('/', [ProductQuantityPriceController::class, 'store'])->name('quantity-prices.store');
                Route::get('/trashed', [ProductQuantityPriceController::class, 'trashed'])->name('quantity-prices.trashed');
                Route::get('/{id}', [ProductQuantityPriceController::class, 'show'])->name('quantity-prices.show');
                Route::put('/{id}', [ProductQuantityPriceController::class, 'update'])->name('quantity-prices.update');
                Route::delete('/{id}', [ProductQuantityPriceController::class, 'destroy'])->name('quantity-prices.destroy');            
                Route::patch('/restore/{id}', [ProductQuantityPriceController::class, 'restore'])->name('quantity-prices.restore');
                Route::delete('/force-delete/{id}', [ProductQuantityPriceController::class, 'forceDelete'])->name('quantity-prices.forceDelete');
            });
        });
        Route::prefix('{product_id}/images')->group(function () {
            Route::get('/', [ProductImageController::class, 'index'])->name('product-images.index');
            Route::middleware(['auth:api_users', 'permission'])->group(function () {
                Route::post('/', [ProductImageController::class, 'store'])->name('product-images.store');
                Route::get('/{id}', [ProductImageController::class, 'show'])->name('product-images.show');
                // Route::put('/{id}', [ProductImageController::class, 'update'])->name('product-images.update');
                Route::delete('/{id}', [ProductImageController::class, 'destroy'])->name('product-images.destroy');
            });
        });
        Route::get('/code/{productCode}', [ProductController::class, 'getByProductCode'])->name('products.get-by-product-code'); 
        Route::get('/{id}/qrcode', [ProductController::class, 'getQrCode'])->name('products.qrcode');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
            Route::patch('/restore/{id}', [ProductController::class, 'restore'])->name('products.restore');
            Route::delete('/force-delete/{id}', [ProductController::class, 'forceDelete'])->name('products.forceDelete');
        });
        Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/slug/{slug}', [ProductController::class, 'getBySlug'])->name('products.get-by-slug');
    });

    Route::prefix('product-categories')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('product-categories.index');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [ProductCategoryController::class, 'store'])->name('product-categories.store');
            Route::get('/trashed', [ProductCategoryController::class, 'trashed'])->name('product-categories.trashed');
        });   
        Route::get('/{id}', [ProductCategoryController::class, 'show'])->name('product-categories.show'); 
        Route::get('/slug/{slug}', [ProductCategoryController::class, 'getBySlug'])->name('product-categories.get-by-slug');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::put('/{id}', [ProductCategoryController::class, 'update'])->name('product-categories.update');
            Route::delete('/{id}', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');
            Route::patch('/restore/{id}', [ProductCategoryController::class, 'restore'])->name('product-categories.restore');
            Route::delete('/force-delete/{id}', [ProductCategoryController::class, 'forceDelete'])->name('product-categories.forceDelete');
        });  
    });

    Route::prefix('product-units')->group(function () {
        Route::get('/', [ProductUnitController::class, 'index'])->name('product-units.index');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [ProductUnitController::class, 'store'])->name('product-units.store');
            Route::get('/trashed', [ProductUnitController::class, 'trashed'])->name('product-units.trashed');
        });   
        Route::get('/{id}', [ProductUnitController::class, 'show'])->name('product-units.show'); 
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::put('/{id}', [ProductUnitController::class, 'update'])->name('product-units.update');
            Route::delete('/{id}', [ProductUnitController::class, 'destroy'])->name('product-units.destroy');
            Route::patch('/restore/{id}', [ProductUnitController::class, 'restore'])->name('product-units.restore');
            Route::delete('/force-delete/{id}', [ProductUnitController::class, 'forceDelete'])->name('product-units.forceDelete');
        });  
    });

    Route::prefix('farms')->group(function () {
        Route::get('/', [FarmController::class, 'index'])->name('farms.index');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [FarmController::class, 'store'])->name('farms.store');
            Route::get('/search', [FarmController::class, 'search'])->name('farms.search');
            Route::get('/trashed', [FarmController::class, 'trashed'])->name('farms.trashed');
        }); 
        Route::get('/{id}', [FarmController::class, 'show'])->name('farms.show');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::put('/{id}', [FarmController::class, 'update'])->name('farms.update');
            Route::delete('/{id}', [FarmController::class, 'destroy'])->name('farms.destroy');    
            Route::patch('/restore/{id}', [FarmController::class, 'restore'])->name('farms.restore');
            Route::delete('/force-delete/{id}', [FarmController::class, 'forceDelete'])->name('farms.forceDelete');
        }); 
    });

    Route::middleware(['auth:api_users', 'permission'])->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{id}', [RoleController::class, 'show'])->name('roles.show');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
    });

    Route::middleware(['auth:api_users', 'permission'])->get('/permissions', [PermissionController::class, 'getPermissionsByPrefix'])->name('permissions.index');

    Route::middleware(['auth:api_users', 'permission'])->prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/trashed', [CustomerController::class, 'trashed'])->name('customers.trashed');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::patch('/restore/{id}', [CustomerController::class, 'restore'])->name('customers.restore');
        Route::delete('/force-delete/{id}', [CustomerController::class, 'forceDelete'])->name('customers.forceDelete');
        Route::put('/change-password/{id}', [CustomerController::class, 'changePassword'])->name('customers.change-password');
    });

    Route::middleware(['auth:api_users', 'permission'])->prefix('customers/{customerId}/addresses')->group(function () {
        Route::get('/', [CustomerAddressController::class, 'index'])->name('customer-addresses.index');
        Route::post('/', [CustomerAddressController::class, 'store'])->name('customer-addresses.store');
        Route::put('/{id}', [CustomerAddressController::class, 'update'])->name('customer-addresses.update');
        Route::delete('/{id}', [CustomerAddressController::class, 'destroy'])->name('customer-addresses.destroy');
    });

    Route::prefix('posts/{postId}/comments')->group(function () {
        Route::get('/', [PostCommentController::class, 'index'])->name('post-comments.index');
        Route::get('/search', [PostCommentController::class, 'search'])->name('post-comments.search');
        Route::delete('/{id}', [PostCommentController::class, 'destroy'])->name('post-comments.destroy');
        Route::middleware('auth:api_customers')->group(function () {
            Route::post('/', [PostCommentController::class, 'store'])->name('post-comments.store');
            Route::put('/{id}', [PostCommentController::class, 'update'])->name('post-comments.update');
        });
    });

    Route::prefix('customer-profile')->middleware('auth:api_customers')->group(function () {
        Route::get('/', [CustomerProfileController::class, 'show'])->name('customer-profile.show');
        Route::put('/', [CustomerProfileController::class, 'update'])->name('customer-profile.update');
        Route::delete('/', [CustomerProfileController::class, 'destroy'])->name('customer-profile.destroy');
        Route::get('/addresses', [CustomerProfileController::class, 'listAddresses'])->name('customer-profile.addresses.index');
        Route::post('/addresses', [CustomerProfileController::class, 'storeAddress'])->name('customer-profile.addresses.store');
        Route::put('/addresses/{id}', [CustomerProfileController::class, 'updateAddress'])->name('customer-profile.addresses.update');
        Route::delete('/addresses/{id}', [CustomerProfileController::class, 'deleteAddress'])->name('customer-profile.addresses.destroy');
    });

    Route::prefix('customer-auth')->group(function () {    
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer-auth.login')->middleware(['throttle:15,1', 'throttle:emailLogin']);
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer-auth.register')->middleware('throttle:500,1');
        Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword'])->name('customer-auth.forgot-password');
        Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('customer-auth.reset-password');
        Route::post('/verify-account', [CustomerAuthController::class, 'verifyAccount'])->name('customer-auth.verify-account');
        Route::post('/refresh-token', [CustomerAuthController::class, 'refreshToken'])->name('customer-auth.refresh-token');
        Route::middleware('auth:api_customers')->group(function () {
            Route::put('/change-password', [CustomerAuthController::class, 'changePassword'])->name('customer-auth.change-password');
            Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer-auth.logout');
        });
    });

    Route::middleware(['auth:api_users', 'permission'])->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/search', [UserController::class, 'search'])->name('users.search');
        Route::get('/search-with-filters', [UserController::class, 'searchWithFilters'])->name('users.searchWithFilters');
        Route::get('/trashed', [UserController::class, 'trashed'])->name('users.trashed');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{usercode}', [UserController::class, 'show'])->name('users.show');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('users.forceDelete');
    });

    Route::middleware(['auth:api_users'])->group(function () {
        Route::get('/user-profile', [UserAuthController::class, 'getProfile'])->name('user-profile.show');
        Route::put('/user-profile', [UserAuthController::class, 'updateProfile'])->name('user-profile.update');
        Route::delete('/user-profile', [UserAuthController::class, 'deleteAccount'])->name('user-profile.destroy');
    });

    Route::prefix('user-auth')->group(function () {
        Route::post('/login', [UserAuthController::class, 'login'])->name('user-auth.login')->middleware(['throttle:15,1', 'throttle:emailLogin']);
        Route::post('/forgot-password', [UserAuthController::class, 'sendResetLink'])->name('user-auth.forgot-password');
        Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])->name('user-auth.reset-password');
        Route::post('/refresh-token', [UserAuthController::class, 'refreshToken'])->name('user-auth.refresh-token');
        Route::middleware('auth:api_users')->group(function () {
            Route::put('/change-password', [UserAuthController::class, 'changePassword'])->name('user-auth.change-password');
            Route::post('/logout', [UserAuthController::class, 'logout'])->name('user-auth.logout');
        });
    });

    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('posts.index');
        Route::get('/hot', [PostController::class, 'getHotPosts'])->name('posts.hot');
        Route::get('/featured', [PostController::class, 'getFeaturedPosts'])->name('posts.featured');
        Route::get('/search', [PostController::class, 'search'])->name('posts.search');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [PostController::class, 'store'])->name('posts.store');
            Route::get('/trashed', [PostController::class, 'trashed'])->name('posts.trashed');
        });
        Route::get('/{id}', [PostController::class, 'show'])->name('posts.show');
        Route::get('/slug/{slug}', [PostController::class, 'getBySlug'])->name('posts.get-by-slug');
        Route::get('/category-slug/{slug}', [PostController::class, 'getByCategorySlug'])->name('posts.get-by-category-slug');
        Route::get('/category/{categoryId}', [PostController::class, 'getByCategory'])->name('posts.get-by-category');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::put('/{id}', [PostController::class, 'update'])->name('posts.update');
            Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
            Route::post('/restore/{id}', [PostController::class, 'restore'])->name('posts.restore');
            Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete'])->name('posts.force-delete');
        });
    });

    Route::prefix('post-categories')->group(function () {
        Route::get('/', [PostCategoryController::class, 'index'])->name('post-categories.index');
        Route::get('/search', [PostCategoryController::class, 'search'])->name('post-categories.search');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::post('/', [PostCategoryController::class, 'store'])->name('post-categories.store');
            Route::get('/trashed', [PostCategoryController::class, 'trashed'])->name('post-categories.trashed');
        });
        Route::get('/{id}', [PostCategoryController::class, 'show'])->name('post-categories.show');
        Route::get('/slug/{slug}', [PostCategoryController::class, 'getBySlug'])->name('post-categories.get-by-slug');
        Route::middleware(['auth:api_users', 'permission'])->group(function () {
            Route::put('/{id}', [PostCategoryController::class, 'update'])->name('post-categories.update');
            Route::delete('/{id}', [PostCategoryController::class, 'destroy'])->name('post-categories.destroy');
            Route::post('/restore/{id}', [PostCategoryController::class, 'restore'])->name('post-categories.restore');
            Route::delete('/force-delete/{id}', [PostCategoryController::class, 'forceDelete'])->name('post-categories.force-delete');
        });
    });
});