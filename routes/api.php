<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Guest có thể xem sách, tác giả, thể loại
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/authors/{id}', [AuthorController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('books/{bookId}/reviews', [ReviewController::class, 'index']);

// User (đăng nhập mới được thêm, sửa giỏ hàng, review...)
Route::middleware(['auth:sanctum', 'role:user,admin'])->group(function () {
    Route::post('books/{bookId}/reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);
    /**
     * CART ROUTES
     */
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);              // Xem giỏ hàng
        Route::post('/add', [CartController::class, 'add']);            // Thêm sản phẩm vào giỏ
        Route::put('/update/{itemId}', [CartController::class, 'update']); // Cập nhật số lượng
        Route::delete('/remove/{itemId}', [CartController::class, 'remove']); // Xóa 1 sản phẩm khỏi giỏ
        Route::delete('/clear', [CartController::class, 'clear']);      // Xóa toàn bộ giỏ
    });

    /**
     * ORDER ROUTES
     */
    Route::prefix('orders')->group(function () {
        Route::post('/checkout', [OrderController::class, 'checkout']); // Tạo đơn hàng từ giỏ hàng
        Route::get('/', [OrderController::class, 'index']);             // Danh sách đơn hàng của user
        Route::get('/{id}', [OrderController::class, 'show']);          // Xem chi tiết đơn hàng
        Route::put('/{id}/update', [OrderController::class, 'update']); // Cập nhật trạng thái đơn (admin)
    });
});

// Admin có toàn quyền CRUD
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('books', BookController::class)->except(['index', 'show']);
    Route::apiResource('authors', AuthorController::class)->except(['index']);
    Route::apiResource('categories', CategoryController::class)->except(['index']);
});


?>