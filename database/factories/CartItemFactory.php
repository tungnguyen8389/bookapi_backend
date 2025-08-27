<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),
            'book_id' => Book::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
        ];
    }
}
