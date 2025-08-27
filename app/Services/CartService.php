<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Book;

class CartService
{
    public function getCartByUser($userId)
    {
        return Cart::with('items.book')->where('user_id', $userId)->firstOrFail();
    }

    public function addItem($userId, $bookId, $quantity = 1)
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('book_id', $bookId)
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'book_id' => $bookId,
                'quantity' => $quantity,
            ]);
        }

        return $this->getCartByUser($userId);
    }

    public function updateItem($userId, $bookId, $quantity)
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();

        $item = CartItem::where('cart_id', $cart->id)
            ->where('book_id', $bookId)
            ->firstOrFail();

        $item->quantity = $quantity;
        $item->save();

        return $this->getCartByUser($userId);
    }

    public function removeItem($userId, $bookId)
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();

        CartItem::where('cart_id', $cart->id)
            ->where('book_id', $bookId)
            ->delete();

        return $this->getCartByUser($userId);
    }

    public function clearCart($userId)
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        $cart->items()->delete();

        return $this->getCartByUser($userId);
    }
}
