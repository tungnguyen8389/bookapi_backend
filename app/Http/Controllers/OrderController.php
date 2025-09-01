<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method' => 'nullable|string',
        ]);

        try {
            $order = $this->orderService->createOrder($request->all());
            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index()
    {
        return response()->json($this->orderService->getOrders());
    }

    public function show($id)
    {
        return response()->json($this->orderService->getOrderDetail($id));
    }

    public function update(Request $request, $id)
{
    $order = Order::findOrFail($id);

    // Chỉ admin mới có quyền
    if (Auth::user()->role != 'admin') {
        return response()->json(['error' => 'Phải là admin mới cập nhật được đơn hàng'], 403);
    }

    $validated = $request->validate([
        'status' => 'in:pending,paid,shipped,completed,cancelled',
        'shipping_status' => 'nullable|string',
        'payment_method' => 'nullable|string',
        'shipping_address' => 'nullable|string',
    ]);

    $order->update($validated);

    return response()->json([
        'message' => 'Cập nhật đơn hàng thành công',
        'order' => $order
    ]);
}

}
