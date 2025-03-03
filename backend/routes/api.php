<?php

use App\Http\Controllers\Api\CultivationLogController;
use App\Http\Controllers\Api\FarmController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductQuantityPriceController;
use App\Http\Controllers\Api\ProductUnitController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
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
    // Product Quantity Prices
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

// Quản lý đợn vị sản phẩm (Chỉ người dùng có quyền)
Route::prefix('product-categories')->group(function () {
    Route::get('/', [ProductCategoryController::class, 'index'])->name('product-categories.index');
    Route::middleware(['auth:api_users', 'permission'])->group(function (){
        Route::post('/', [ProductCategoryController::class, 'store'])->name('product-categories.store');
        Route::get('/trashed', [ProductCategoryController::class, 'trashed'])->name('product-categories.trashed'); // GET /api/product-categories/trashed
    });   
    Route::get('/{id}', [ProductCategoryController::class, 'show'])->name('product-categories.show'); 
    Route::get('/slug/{slug}', [ProductCategoryController::class, 'getBySlug'])->name('product-categories.get-by-slug'); // GET /api/product-categories/slug/{slug}
    Route::middleware(['auth:api_users', 'permission'])->group(function (){
        Route::put('/{id}', [ProductCategoryController::class, 'update'])->name('product-categories.update');
        Route::delete('/{id}', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');
        Route::patch('/restore/{id}', [ProductCategoryController::class, 'restore'])->name('product-categories.restore'); // PATCH /api/product-categories/restore/{id}
        Route::delete('/force-delete/{id}', [ProductCategoryController::class, 'forceDelete'])->name('product-categories.forceDelete'); // DELETE /api/product-categories/force-delete/{id}
    });  
});



// Quản lý đợn vị sản phẩm (Chỉ người dùng có quyền)
Route::prefix('product-units')->group(function () {
    Route::get('/', [ProductUnitController::class, 'index'])->name('product-units.index');
    Route::middleware(['auth:api_users', 'permission'])->group(function (){
        Route::post('/', [ProductUnitController::class, 'store'])->name('product-units.store');
        Route::get('/trashed', [ProductUnitController::class, 'trashed'])->name('product-units.trashed'); // GET /api/product-units/trashed
    });   
    Route::get('/{id}', [ProductUnitController::class, 'show'])->name('product-units.show'); 
    Route::middleware(['auth:api_users', 'permission'])->group(function (){
        Route::put('/{id}', [ProductUnitController::class, 'update'])->name('product-units.update');
        Route::delete('/{id}', [ProductUnitController::class, 'destroy'])->name('product-units.destroy');
        Route::patch('/restore/{id}', [ProductUnitController::class, 'restore'])->name('product-units.restore'); // PATCH /api/product-units/restore/{id}
        Route::delete('/force-delete/{id}', [ProductUnitController::class, 'forceDelete'])->name('product-units.forceDelete'); // DELETE /api/product-units/force-delete/{id}
    });  
});

// Quản lý nông trại (Chỉ người dùng có quyền)
Route::prefix('farms')->group(function () {
    Route::get('/', [FarmController::class, 'index'])->name('farms.index');
    Route::middleware(['auth:api_users', 'permission'])->group(function () {
        Route::post('/', [FarmController::class, 'store'])->name('farms.store');
        Route::get('/search', [FarmController::class, 'search'])->name('farms.search');
        Route::get('/trashed', [FarmController::class, 'trashed'])->name('farms.trashed');// GET /api/farms/trashed
    }); 
    Route::get('/{id}', [FarmController::class, 'show'])->name('farms.show');
    Route::middleware(['auth:api_users', 'permission'])->group(function () {
        Route::put('/{id}', [FarmController::class, 'update'])->name('farms.update');
        Route::delete('/{id}', [FarmController::class, 'destroy'])->name('farms.destroy');    
        Route::patch('/restore/{id}', [FarmController::class, 'restore'])->name('farms.restore'); // PATCH /api/farms/restore/{id}
        Route::delete('/force-delete/{id}', [FarmController::class, 'forceDelete'])->name('farms.forceDelete'); // DELETE /api/farms/force-delete/{id}
    }); 
});


// Quản lý vai trò (Chỉ người dùng có quyền)
Route::middleware(['auth:api_users', 'permission'])->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
});

// Lấy danh sách quyền (Chỉ người dùng đã đăng nhập)
Route::middleware(['auth:api_users', 'permission'])->get('/permissions', [PermissionController::class, 'getPermissionsByPrefix'])->name('permissions.index');

// Quản lý khách hàng (Chỉ Admin hoặc User có quyền)
Route::middleware(['auth:api_users', 'permission'])->prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/trashed', [CustomerController::class, 'trashed'])->name('customers.trashed'); // GET /api/customers/trashed
    Route::get('/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::patch('/restore/{id}', [CustomerController::class, 'restore'])->name('customers.restore'); // PATCH /api/customers/restore/{id}
    Route::delete('/force-delete/{id}', [CustomerController::class, 'forceDelete'])->name('customers.forceDelete'); // DELETE /api/customers/force-delete/{id}
    Route::put('/change-password/{id}', [CustomerController::class, 'changePassword'])->name('customers.change-password');
});

// Quản lý địa chỉ khách hàng (Chỉ Admin hoặc User đăng nhập)
Route::middleware(['auth:api_users', 'permission'])->prefix('customers/{customerId}/addresses')->group(function () {
    Route::get('/', [CustomerAddressController::class, 'index'])->name('customer-addresses.index');
    Route::post('/', [CustomerAddressController::class, 'store'])->name('customer-addresses.store');
    Route::put('/{id}', [CustomerAddressController::class, 'update'])->name('customer-addresses.update');
    Route::delete('/{id}', [CustomerAddressController::class, 'destroy'])->name('customer-addresses.destroy');
});

// Route bình luận bài viết
Route::prefix('posts/{postId}/comments')->group(function () {
    Route::get('/', [PostCommentController::class, 'index'])->name('post-comments.index');
    Route::get('/search', [PostCommentController::class, 'search'])->name('post-comments.search'); // GET /api/posts-comments/search
    // Kiểm tra trong controller
    Route::delete('/{id}', [PostCommentController::class, 'destroy'])->name('post-comments.destroy');
    Route::middleware('auth:api_customers')->group(function () {
        Route::post('/', [PostCommentController::class, 'store'])->name('post-comments.store');
        Route::put('/{id}', [PostCommentController::class, 'update'])->name('post-comments.update');
    });
});

// Routes chỉnh sửa thông tin dành cho khách hàng đã đăng nhập
Route::prefix('customer-profile')->middleware('auth:api_customers')->group(function () {
    Route::get('/', [CustomerProfileController::class, 'show'])->name('customer-profile.show');
    Route::put('/', [CustomerProfileController::class, 'update'])->name('customer-profile.update');
    Route::delete('/', [CustomerProfileController::class, 'destroy'])->name('customer-profile.destroy');
    Route::get('/addresses', [CustomerProfileController::class, 'listAddresses'])->name('customer-profile.addresses.index');
    Route::post('/addresses', [CustomerProfileController::class, 'storeAddress'])->name('customer-profile.addresses.store');
    Route::put('/addresses/{id}', [CustomerProfileController::class, 'updateAddress'])->name('customer-profile.addresses.update');
    Route::delete('/addresses/{id}', [CustomerProfileController::class, 'deleteAddress'])->name('customer-profile.addresses.destroy');
});

// Route xác thực khách hàng
Route::prefix('customer-auth')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer-auth.login'); // POST /api/customer-auth/login
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer-auth.register'); // POST /api/customer-auth/register
    Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword'])->name('customer-auth.forgot-password'); // POST /api/customer-auth/forgot-password
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('customer-auth.reset-password'); // POST /api/customer-auth/reset-password
    Route::post('/verify-account', [CustomerAuthController::class, 'verifyAccount'])->name('customer-auth.verify-account'); // POST /api/customer-auth/verify-account
    Route::post('/refresh-token', [CustomerAuthController::class, 'refreshToken'])->name('customer-auth.refresh-token'); // POST /api/customer-auth/refresh-token
    Route::middleware('auth:api_customers')->group(function () {
        Route::put('/change-password', [CustomerAuthController::class, 'changePassword'])->name('customer-auth.change-password'); // PUT /api/customer-auth/change-password
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer-auth.logout'); // POST /api/customer-auth/logout
    });
});

// Route quản lý người dùng (Chỉ người dùng đã đăng nhập)
Route::middleware(['auth:api_users', 'permission'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index'); // GET /api/users/
    Route::get('/search', [UserController::class, 'search'])->name('users.search'); // GET /api/users/search
    Route::get('/search-with-filters', [UserController::class, 'searchWithFilters'])->name('users.searchWithFilters'); // GET /api/users/search-with-filters
    Route::get('/trashed', [UserController::class, 'trashed'])->name('users.trashed'); // GET /api/users/trashed
    Route::post('/', [UserController::class, 'store'])->name('users.store'); // POST /api/users/
    Route::get('/{usercode}', [UserController::class, 'show'])->name('users.show'); // GET /api/users/{usercode}
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update'); // PUT /api/users/{id}
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy'); // DELETE /api/users/{id}
    Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('users.restore'); // PATCH /api/users/restore/{id}
    Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('users.forceDelete'); // DELETE /api/users/force-delete/{id}
});

// Routes chỉnh sửa thông tin dành cho người dùng đã đăng nhập
Route::middleware(['auth:api_users'])->group(function () {
    Route::get('/user-profile', [UserAuthController::class, 'getProfile'])->name('user-profile.show'); // GET /api/user-profile
    Route::put('/user-profile', [UserAuthController::class, 'updateProfile'])->name('user-profile.update'); // PUT /api/user-profile
    Route::delete('/user-profile', [UserAuthController::class, 'deleteAccount'])->name('user-profile.destroy'); // DELETE /api/user-profile
});

// Routes dành cho xác thực người dùng
Route::prefix('user-auth')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login'])->name('user-auth.login'); // POST /api/user-auth/login
    Route::post('/forgot-password', [UserAuthController::class, 'sendResetLink'])->name('user-auth.forgot-password'); // POST /api/user-auth/forgot-password
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])->name('user-auth.reset-password'); // POST /api/user-auth/reset-password
    Route::post('/refresh-token', [UserAuthController::class, 'refreshToken'])->name('user-auth.refresh-token'); // POST /api/user-auth/refresh-token
    Route::middleware('auth:api_users')->group(function () {
        Route::put('/change-password', [UserAuthController::class, 'changePassword'])->name('user-auth.change-password'); // PUT /api/user-auth/change-password
        Route::post('/logout', [UserAuthController::class, 'logout'])->name('user-auth.logout'); // POST /api/user-auth/logout
    });
});

// Route bài viết
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index'); // GET /api/posts
    Route::get('/hot', [PostController::class, 'getHotPosts'])->name('posts.hot'); // GET /api/posts/hot
    Route::get('/featured', [PostController::class, 'getFeaturedPosts'])->name('posts.featured'); // GET /api/posts/featured
    Route::get('/search', [PostController::class, 'search'])->name('posts.search'); // GET /api/posts/search
    Route::middleware('auth:api_users')->group(function () {
        Route::post('/', [PostController::class, 'store'])->name('posts.store'); // POST /api/posts
        Route::get('/trashed', [PostController::class, 'trashed'])->name('posts.trashed'); // GET /api/posts/trashed
    });
    Route::get('/{id}', [PostController::class, 'show'])->name('posts.show'); // GET /api/posts/{id}
    Route::get('/slug/{slug}', [PostController::class, 'getBySlug'])->name('posts.get-by-slug'); // GET /api/posts/slug/{slug}
    Route::get('/category-slug/{slug}', [PostController::class, 'getByCategorySlug'])->name('posts.get-by-category-slug'); // GET /api/posts/category-slug/{slug}
    Route::get('/category/{categoryId}', [PostController::class, 'getByCategory'])->name('posts.get-by-category'); // GET /api/posts/category/{categoryId}
    Route::middleware('auth:api_users')->group(function () {
        Route::put('/{id}', [PostController::class, 'update'])->name('posts.update'); // PUT /api/posts/{id}
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy'); // DELETE /api/posts/{id}
        Route::post('/restore/{id}', [PostController::class, 'restore'])->name('posts.restore'); // POST /api/posts/restore/{id}
        Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete'])->name('posts.force-delete'); // DELETE /api/posts/force-delete/{id}
    });
});

// Route loại bài viết
Route::prefix('post-categories')->group(function () {
    // Các route không tham số
    Route::get('/', [PostCategoryController::class, 'index'])->name('post-categories.index'); // GET /api/post-categories
    Route::get('/search', [PostCategoryController::class, 'search'])->name('post-categories.search'); // GET /api/post-categories/search
    Route::middleware(['auth:api_users', 'permission'])->group(function () {
        Route::post('/', [PostCategoryController::class, 'store'])->name('post-categories.store'); // POST /api/post-categories
        Route::get('/trashed', [PostCategoryController::class, 'trashed'])->name('post-categories.trashed'); // GET /api/post-categories/trashed
    });
    // Các route có tham số
    Route::get('/{id}', [PostCategoryController::class, 'show'])->name('post-categories.show'); // GET /api/post-categories/{id}
    Route::get('/slug/{slug}', [PostCategoryController::class, 'getBySlug'])->name('post-categories.get-by-slug'); // GET /api/post-categories/slug/{slug}
    Route::middleware(['auth:api_users', 'permission'])->group(function () {
        Route::put('/{id}', [PostCategoryController::class, 'update'])->name('post-categories.update'); // PUT /api/post-categories/{id}
        Route::delete('/{id}', [PostCategoryController::class, 'destroy'])->name('post-categories.destroy'); // DELETE /api/post-categories/{id}
        Route::post('/restore/{id}', [PostCategoryController::class, 'restore'])->name('post-categories.restore'); // POST /api/post-categories/restore/{id}
        Route::delete('/force-delete/{id}', [PostCategoryController::class, 'forceDelete'])->name('post-categories.force-delete'); // DELETE /api/post-categories/force-delete/{id}
    });
});