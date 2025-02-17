<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostCategoryController;
use App\Http\Controllers\Api\UserController;

Route::middleware(['auth:api_users'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index'); // GET /api/users/ 
    Route::get('/search', [UserController::class, 'search'])->name('users.search'); // GET /api/users/search 
    Route::get('/search-with-filters', [UserController::class, 'searchWithFilters'])->name('users.searchWithFilters'); // GET /api/users/search-with-filters 
    Route::get('/{usercode}', [UserController::class, 'show'])->name('users.show'); // GET /api/users/{usercode} 
    Route::post('/', [UserController::class, 'store'])->name('users.store'); // POST /api/users/
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update'); // PUT /api/users/{id}
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy'); // DELETE /api/users/{id}
    Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('users.restore'); // PATCH /api/users/restore/{id}
    Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('users.forceDelete'); // DELETE /api/users/force-delete/{id}
});

// Route reset password 
Route::post('/password/forgot', [UserAuthController::class, 'sendResetLink']); // POST /api/password/forgot
Route::post('/password/reset', [UserAuthController::class, 'resetPassword']); // POST /api/password/reset

// Routes dành cho xác thực users
Route::prefix('userauth')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login'])->name('login'); // POST /api/userauth/login
    Route::post('/refresh-token', [UserAuthController::class, 'refreshToken']); // POST /api/userauth/refresh-token
    Route::middleware('auth:api_users')->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout']); // POST /api/userauth/logout
        Route::get('/user', [UserAuthController::class, 'user']); // GET /api/userauth/user
    });
});

// Các route công khai (Không yêu cầu xác thực)
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']); // GET /api/posts
    Route::get('/hot', [PostController::class, 'getHotPosts']); // GET /api/posts/hot
    Route::get('/featured', [PostController::class, 'getFeaturedPosts']); // GET /api/posts/featured
    Route::get('/search', [PostController::class, 'search']); // GET /api/posts/search
    Route::get('/{id}', [PostController::class, 'show']); // GET /api/posts/{id}
    Route::get('/slug/{slug}', [PostController::class, 'getBySlug']); // GET /api/posts/slug/{slug}
    Route::get('/category-slug/{slug}', [PostController::class, 'getByCategorySlug']); // GET /api/posts/category-slug/{slug}
    Route::get('/category/{categoryId}', [PostController::class, 'getByCategory']); // GET /api/posts/category/{categoryId}
    // Các route yêu cầu xác thực
    Route::middleware('auth:api_users')->group(function () {
        Route::post('/', [PostController::class, 'store']); // POST /api/posts
        Route::put('/{id}', [PostController::class, 'update']); // PUT /api/posts/{id}
        Route::delete('/{id}', [PostController::class, 'destroy']); // DELETE /api/posts/{id}
        Route::post('/restore/{id}', [PostController::class, 'restore']); // POST /api/posts/restore/{id}
        Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete']); // DELETE /api/posts/force-delete/{id}
    });
});

Route::prefix('postcategories')->group(function () {
    Route::get('/', [PostCategoryController::class, 'index']); // GET /api/postcategories
    Route::get('/search',[PostCategoryController::class, 'search']);// GET /api/postcategories/search
    Route::get('/{id}', [PostCategoryController::class, 'show']); // GET /api/postcategories/{id}    
    Route::get('/slug/{slug}', [PostCategoryController::class, 'getBySlug']); // GET /api/postcategories/slug/{slug}

    // Các route yêu cầu xác thực
    Route::middleware('auth:api_users')->group(function () {
        Route::post('/', [PostCategoryController::class, 'store']); // POST /api/postcategories
        Route::put('/{id}', [PostCategoryController::class, 'update']); // PUT /api/postcategories/{id}
        Route::delete('/{id}', [PostCategoryController::class, 'destroy']); // DELETE /api/postcategories/{id}      
        Route::post('/restore/{id}', [PostCategoryController::class, 'restore']); // POST /api/postcategories/restore/{id}   
        Route::delete('/force-delete/{id}', [PostCategoryController::class, 'forceDelete']); // DELETE /api/postcategories/force-delete/{id} 
    });
});
