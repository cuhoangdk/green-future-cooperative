<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostCategoryController;

// Routes dành cho xác thực cooperative_members
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

// Các route công khai (Không yêu cầu xác thực)
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']); // GET /api/posts
    Route::get('/hot', [PostController::class, 'getHotPosts']); // GET /api/posts/hot
    Route::get('/featured', [PostController::class, 'getFeaturedPosts']); // GET /api/posts/featured
    Route::get('/{id}', [PostController::class, 'show']); // GET /api/posts/{id}
    Route::get('/slug/{slug}', [PostController::class, 'getBySlug']); // GET /api/posts/slug/{slug}
    Route::get('/category/{categoryId}', [PostController::class, 'getByCategory']); // GET /api/posts/category/{categoryId}
    // Các route yêu cầu xác thực
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [PostController::class, 'store']); // Tạo bài viết
        Route::put('/{id}', [PostController::class, 'update']); // Cập nhật bài viết
        Route::delete('/{id}', [PostController::class, 'destroy']); // Xóa bài viết
        Route::post('/restore/{id}', [PostController::class, 'restore']); // Khôi phục bài viết
        Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete']); // Xóa vĩnh viễn bài viết
    });
});

Route::prefix('postcategories')->group(function () {
    Route::get('/', [PostCategoryController::class, 'index']); // GET /api/postcategories
    Route::get('/{id}', [PostCategoryController::class, 'show']); // GET /api/postcategories/{id}    
    Route::get('/slug/{slug}', [PostCategoryController::class, 'getBySlug']); // GET /api/postcategories/slug/{slug}

    // Các route yêu cầu xác thực
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [PostCategoryController::class, 'store']); // Thêm danh mục
        Route::put('/{id}', [PostCategoryController::class, 'update']); // Cập nhật danh mục
        Route::delete('/{id}', [PostCategoryController::class, 'destroy']); // Xóa danh mục        
        Route::post('/restore/{id}', [PostCategoryController::class, 'restore']); // Khôi phục danh mục
        Route::delete('/force-delete/{id}', [PostCategoryController::class, 'forceDelete']); // Xóa vĩnh viễn danh mục
    });
});
