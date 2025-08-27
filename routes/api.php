<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;

Route::apiResource('books', BookController::class);
Route::apiResource('authors', AuthorController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('reviews', ReviewController::class);
Route::prefix('carts/{userId}')->group(function () {
    Route::get('/', [CartController::class, 'show']);
    Route::post('add', [CartController::class, 'addItem']);
    Route::put('update/{bookId}', [CartController::class, 'updateItem']);
    Route::delete('remove/{bookId}', [CartController::class, 'removeItem']);
    Route::delete('clear', [CartController::class, 'clear']);
});

?>