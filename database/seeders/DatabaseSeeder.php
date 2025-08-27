<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Author, Category, Book, Review, Cart, CartItem, Order, OrderItem, Transaction};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo categories và authors trước
        $categories = Category::factory(5)->create();
        $authors = Author::factory(10)->create();

        // Tạo books
        $books = Book::factory(20)->create([
            'category_id' => $categories->random()->id,
            'author_id' => $authors->random()->id,
        ]);

        // Tạo reviews
        Review::factory(30)->create([
            'book_id' => $books->random()->id,
        ]);

        // Tạo orders + order items + transactions
        $orders = Order::factory(10)->create();

        foreach ($orders as $order) {
            $items = OrderItem::factory(3)->create([
                'order_id' => $order->id,
                'book_id' => $books->random()->id,
            ]);

            Transaction::factory()->create([
                'order_id' => $order->id,
                'amount' => $items->sum(fn($item) => $item->price * $item->quantity),
            ]);
        }

        // Tạo carts và cart items
        Cart::factory(10)->create()->each(function ($cart) {
            $books = Book::inRandomOrder()->take(5)->get();
            foreach ($books as $book) {
                CartItem::factory()->create([
                    'cart_id' => $cart->id,
                    'book_id' => $book->id,
                ]);
            }
});
    }
}
