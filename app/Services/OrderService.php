<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder($data)
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::where('user_id', Auth::id())->with('items.book')->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }

            $totalPrice = 0;

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => 0,
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'shipping_status' => 'not_shipped',
            ]);

            foreach ($cart->items as $item) {
                if ($item->book->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$item->book->title} không đủ số lượng");
                }

                $totalPrice += $item->book->price * $item->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price,
                ]);

                // Trừ stock sách
                $item->book->decrement('stock', $item->quantity);
            }

            $order->update(['total_price' => $totalPrice]);

            // Xoá giỏ hàng sau khi đặt hàng
            $cart->items()->delete();

            return $order->load('items.book');
        });
    }

    public function getOrders()
    {
        return Order::where('user_id', Auth::id())->with('items.book')->get();
    }

    public function getOrderDetail($orderId)
    {
        return Order::where('user_id', Auth::id())
            ->with('items.book')
            ->findOrFail($orderId);
    }

    /**
     * Lấy tất cả đơn hàng từ database với pagination, filter theo status và tìm kiếm.
     */
    public function getAllOrders($perPage = 10, $statusArray = null, $page = 1, $search = null)
    {
        $query = Order::query();

        // Filter theo array status nếu có
        if ($statusArray && is_array($statusArray) && count($statusArray) > 0) {
            $query->whereIn('status', $statusArray);
        }

        // Tìm kiếm theo order ID, user name, hoặc ngày tạo
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                // Tìm theo order ID
                $q->where('id', 'LIKE', '%' . $search . '%')
                    // Tìm theo tên user
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('email', 'LIKE', '%' . $search . '%');
                    })
                    // Tìm theo ngày tạo (format YYYY-MM-DD)
                    ->orWhere('created_at', 'LIKE', '%' . $search . '%');
            });
        }

        // Sort theo ngày tạo gần nhất lên đầu
        $query->orderBy('created_at', 'desc');

        // Load tất cả thông tin của orders bao gồm items, books với author và category
        $query->with([
            'items.book',
            'user'
        ]);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
