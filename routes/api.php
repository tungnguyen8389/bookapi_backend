<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Guest có thể xem sách, tác giả, thể loại
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);

// User (đăng nhập mới được thêm, sửa giỏ hàng, review...)
Route::middleware(['auth:sanctum', 'role:user,admin'])->group(function () {
    Route::post('/books/{id}/review', [BookController::class, 'review']); // ví dụ
});

// Admin có toàn quyền CRUD
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('books', BookController::class)->except(['index', 'show']);
    Route::apiResource('authors', AuthorController::class)->except(['index']);
    Route::apiResource('categories', CategoryController::class)->except(['index']);
});


?>