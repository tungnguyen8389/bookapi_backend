<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show($userId)
    {
        return response()->json($this->cartService->getCartByUser($userId));
    }

    public function addItem(Request $request, $userId)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $cart = $this->cartService->addItem(
            $userId,
            $data['book_id'],
            $data['quantity'] ?? 1
        );

        return response()->json($cart);
    }

    public function updateItem(Request $request, $userId, $bookId)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->cartService->updateItem($userId, $bookId, $data['quantity']);

        return response()->json($cart);
    }

    public function removeItem($userId, $bookId)
    {
        $cart = $this->cartService->removeItem($userId, $bookId);

        return response()->json($cart);
    }

    public function clear($userId)
    {
        $cart = $this->cartService->clearCart($userId);

        return response()->json($cart);
    }
}
