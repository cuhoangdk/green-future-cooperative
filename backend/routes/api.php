<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostCategoryController;

Route::middleware('api')->group(function () {
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index']); // GET /api/posts
        Route::get('/{id}', [PostController::class, 'show']); // GET /api/posts/{id}
        Route::post('/', [PostController::class, 'store']); // POST /api/posts
        Route::put('/{id}', [PostController::class, 'update']); // PUT /api/posts/{id}
        Route::delete('/{id}', [PostController::class, 'destroy']); // DELETE /api/posts/{id}
        Route::get('/slug/{slug}', [PostController::class, 'getBySlug']); // GET /api/posts/slug/{slug}
        Route::get('/category/{categoryId}', [PostController::class, 'getByCategory']); // GET /api/posts/category/{categoryId}
        Route::post('/restore/{id}', [PostController::class, 'restore']); // POST /api/posts/restore/{id}
        Route::delete('/force-delete/{id}', [PostController::class, 'forceDelete']); // DELETE /api/posts/force-delete/{id}
    });       
    Route::prefix('postcategories')->group(function () {
        Route::get('/', [PostCategoryController::class, 'index']); // GET /api/posts
        Route::get('/{id}', [PostCategoryController::class, 'show']); // GET /api/posts/{id}
        Route::post('/', [PostCategoryController::class, 'store']); // POST /api/posts
        Route::put('/{id}', [PostCategoryController::class, 'update']); // PUT /api/posts/{id}
        Route::delete('/{id}', [PostCategoryController::class, 'destroy']); // DELETE /api/posts/{id}
        Route::get('/slug/{slug}', [PostCategoryController::class, 'getBySlug']); // GET /api/posts/slug/{slug}    
        Route::post('/restore/{id}', [PostCategoryController::class, 'restore']); // POST /api/posts/restore/{id}
        Route::delete('/force-delete/{id}', [PostCategoryController::class, 'forceDelete']); // DELETE /api/posts/force-delete/{id}
    });
});

