<?php
namespace App\Http\Controllers;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        return response()->json($this->cartService->getCart());
    }

    public function add(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'nullable|integer|min:1',
        ]);
        $cart = $this->cartService->addItem($request->book_id, $request->quantity ?? 1);

        return response()->json($cart);
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);
        $cart = $this->cartService->updateItem($itemId, $request->quantity);
        return response()->json($cart);
    }

    public function remove($itemId)
    {
        $cart = $this->cartService->removeItem($itemId);
        return response()->json($cart);
    }

    public function clear()
    {
        $cart = $this->cartService->clearCart();
        return response()->json($cart);
    }
}
