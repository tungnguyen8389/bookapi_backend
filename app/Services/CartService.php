<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCart()
    {
        $cart = \App\Models\Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cart->load('items.book');

        // có thể trả thẳng $cart (đã có subtotal & items_count)
        // hoặc kèm summary:
        return [
            'cart'    => $cart,
            'summary' => [
                'items_count' => $cart->items_count,
                'subtotal'    => $cart->subtotal,
            ],
        ];
    }

    public function addItem($bookId, $quantity = 1)
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $book = Book::findOrFail($bookId);

        if ($book->stock <= 0) {
            throw new \Exception('Sản phẩm đã hết hàng');
        }

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

        return $cart->load('items.book');
    }

    public function updateItem($itemId, $quantity)
    {
        $item = CartItem::findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        return $item->cart->load('items.book');
    }

    public function removeItem($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $cart = $item->cart;
        $item->delete();

        return $cart->load('items.book');
    }

    public function clearCart()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cart->items()->delete();

        return $cart;
    }
}
