<?php

use Illuminate\Support\Facades\Route;
// Route::get('/', function () {
//     return view('welcome');
// });

use Illuminate\Http\Request;
use App\Http\Controllers\Api\PostController;
Route::get('login', function () {
    return view('auth.login');
})->name('login');
// routes/api.php
Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index']); // GET /api/posts
        Route::get('/{id}', [PostController::class, 'show']); // GET /api/posts/{id}
        Route::post('/', [PostController::class, 'store']); // POST /api/posts
        Route::put('/{id}', [PostController::class, 'update']); // PUT /api/posts/{id}
        Route::delete('/{id}', [PostController::class, 'destroy']); // DELETE /api/posts/{id}
        Route::get('/slug/{slug}', [PostController::class, 'getBySlug']); // GET /api/posts/slug/{slug}
        Route::get('/category/{categoryId}', [PostController::class, 'getByCategory']); // GET /api/posts/category/{categoryId}
    });
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
