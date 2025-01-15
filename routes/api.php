<?php

use App\Http\Controllers\Api\PostController;
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']); // GET /api/posts
    Route::get('/{id}', [PostController::class, 'show']); // GET /api/posts/{id}
    Route::post('/', [PostController::class, 'store']); // POST /api/posts
    Route::put('/{id}', [PostController::class, 'update']); // PUT /api/posts/{id}
    Route::delete('/{id}', [PostController::class, 'destroy']); // DELETE /api/posts/{id}
    Route::get('/slug/{slug}', [PostController::class, 'getBySlug']); // GET /api/posts/slug/{slug}
    Route::get('/category/{categoryId}', [PostController::class, 'getByCategory']); // GET /api/posts/category/{categoryId}
});
