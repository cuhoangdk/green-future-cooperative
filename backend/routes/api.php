<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostCategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\PostCommentController;

// Route bình luận bài viết
Route::prefix('posts/{postId}/comments')->group(function () {
    Route::get('/', [PostCommentController::class, 'index']);
    // Kiểm tra trong controller
    Route::delete('/{id}', [PostCommentController::class, 'destroy']); 
    Route::middleware('auth:api_customers')->group(function () {
        Route::post('/', [PostCommentController::class, 'store']);
        Route::put('/{id}', [PostCommentController::class, 'update']);
    });
});

// Routes chỉnh sửa thông tin dành cho khách hàng đã đăng nhập
Route::prefix('customer-profile')->middleware('auth:api_customers')->group(function () {
    Route::get('/', [CustomerProfileController::class, 'show']); // GET /api/customer-profile
    Route::put('/', [CustomerProfileController::class, 'update']); // PUT /api/customer-profile
    Route::delete('/', [CustomerProfileController::class, 'destroy']); // DELETE /api/customer-profile
    Route::get('/addresses', [CustomerProfileController::class, 'listAddresses']); // GET /api/customer-profile/addresses 
    Route::post('/addresses', [CustomerProfileController::class, 'storeAddress']); // POST /api/customer-profile/addresses
    Route::put('/addresses/{id}', [CustomerProfileController::class, 'updateAddress']); // PUT /api/customer-profile/addresses/{id}
    Route::delete('/addresses/{id}', [CustomerProfileController::class, 'deleteAddress']); // DELETE /api/customer-profile/addresses/{id}
});

//Route khách hàng
Route::prefix('customer-auth')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login']); // POST /api/customer-auth/login
    Route::post('/register', [CustomerAuthController::class, 'register']); // POST /api/customer-auth/register
    Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword']); // POST /api/customer-auth/forgot-password
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword']); // POST /api/customer-auth/reset-password
    Route::post('/verify-account', [CustomerAuthController::class, 'verifyAccount']); // POST /api/customer-auth/verify-account
    Route::middleware('auth:api_customers')->group(function (){
        Route::put('/change-password', [CustomerAuthController::class, 'changePassword']); // PUT /api/customer-auth/change-password
        Route::post('/refresh-token', [CustomerAuthController::class, 'refreshToken']); // POST /api/customer-auth/refresh-token
        Route::post('/logout', [CustomerAuthController::class, 'logout']); // POST /api/customer-auth/logout
    });
});

// Route người dùng
Route::middleware(['auth:api_users'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index'); // GET /api/users/ 
    Route::get('/search', [UserController::class, 'search'])->name('users.search'); // GET /api/users/search 
    Route::get('/search-with-filters', [UserController::class, 'searchWithFilters'])->name('users.searchWithFilters'); // GET /api/users/search-with-filters 
    Route::get('/trashed', [UserController::class, 'trashed']);// GET /api/users/trashed
    Route::post('/', [UserController::class, 'store'])->name('users.store'); // POST /api/users/
    Route::get('/{usercode}', [UserController::class, 'show'])->name('users.show'); // GET /api/users/{usercode}
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update'); // PUT /api/users/{id}
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy'); // DELETE /api/users/{id}
    Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('users.restore'); // PATCH /api/users/restore/{id}
    Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('users.forceDelete'); // DELETE /api/users/force-delete/{id}
});
// Routes chỉnh sửa thông tin dành cho người dùng đã đăng nhập
Route::middleware(['auth:api_users'])->group(function () {
    Route::get('/user-profile', [UserAuthController::class, 'getProfile']); // GET /api/user-profile
    Route::put('/user-profile', [UserAuthController::class, 'updateProfile']); // PUT /api/user-profile
    Route::delete('/user-profile', [UserAuthController::class, 'deleteAccount']); // DELETE /api/user-profile    
});

// Routes dành cho xác thực người dùng
Route::prefix('user-auth')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login'])->name('login'); // POST /api/user-auth/login    
    Route::post('/forgot-password', [UserAuthController::class, 'sendResetLink']); // POST /api/user-auth/forgot-password
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword']); // POST /api/user-auth/reset-password    
    Route::middleware('auth:api_users')->group(function () {
        Route::put('/change-password', [UserAuthController::class, 'changePassword']); // PUT /api/user-auth/change-password
        Route::post('/refresh-token', [UserAuthController::class, 'refreshToken']); // POST /api/user-auth/refresh-token
        Route::post('/logout', [UserAuthController::class, 'logout']); // POST /api/use-rauth/logout
    });
});

// Route bài viết
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']); // GET /api/posts
    Route::get('/hot', [PostController::class, 'getHotPosts']); // GET /api/posts/hot
    Route::get('/featured', [PostController::class, 'getFeaturedPosts']); // GET /api/posts/featured
    Route::get('/search', [PostController::class, 'search']); // GET /api/posts/search
    Route::middleware('auth:api_users')->group(function () {
        Route::post('/', [PostController::class, 'store']); // POST /api/posts
        Route::get('/trashed', [PostController::class, 'trashed']);// GET /api/posts/trashed
    });
    Route::get('/{id}', [PostController::class, 'show']); // GET /api/posts/{id}
    Route::get('/slug/{slug}', [PostController::class, 'getBySlug']); // GET /api/posts/slug/{slug}
    Route::get('/category-slug/{slug}', [PostController::class, 'getByCategorySlug']); // GET /api/posts/category-slug/{slug}
    Route::get('/category/{categoryId}', [PostController::class, 'getByCategory']); // GET /api/posts/category/{categoryId}
    Route::middleware('auth:api_users')->group(function () {
        Route::put('/{id}', [PostController::class, 'update']); // PUT /api/posts/{id}
        Route::delete('/{id}', [PostController::class, 'destroy']); // DELETE /api/posts/{id}
        Route::post('/restore/{id}', [PostController::class, 'restore']); // POST /api/posts/restore/{id}
        Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete']); // DELETE /api/posts/force-delete/{id}
    });
});
// Route loại bài viết
Route::prefix('post-categories')->group(function () {
    // Các route không tham số
    Route::get('/', [PostCategoryController::class, 'index']); // GET /api/postcategories    
    Route::get('/search',[PostCategoryController::class, 'search']);// GET /api/postcategories/search
    Route::middleware('auth:api_users')->group(function () {
        Route::post('/', [PostCategoryController::class, 'store']); // POST /api/postcategories 
        Route::get('/trashed', [PostCategoryController::class, 'trashed']); // GET /api/postcategories/trashed 
    });
    // Các route có tham số
    Route::get('/{id}', [PostCategoryController::class, 'show']); // GET /api/postcategories/{id}    
    Route::get('/slug/{slug}', [PostCategoryController::class, 'getBySlug']); // GET /api/postcategories/slug/{slug}
    Route::middleware('auth:api_users')->group(function () {               
        Route::put('/{id}', [PostCategoryController::class, 'update']); // PUT /api/postcategories/{id}
        Route::delete('/{id}', [PostCategoryController::class, 'destroy']); // DELETE /api/postcategories/{id}         
        Route::post('/restore/{id}', [PostCategoryController::class, 'restore']); // POST /api/postcategories/restore/{id}   
        Route::delete('/force-delete/{id}', [PostCategoryController::class, 'forceDelete']); // DELETE /api/postcategories/force-delete/{id} 
    });
});
