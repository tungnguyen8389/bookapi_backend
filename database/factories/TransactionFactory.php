<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => $this->faker->randomFloat(2, 20, 500),
            'method' => $this->faker->randomElement(['credit_card', 'paypal', 'cod']),
            'status' => $this->faker->randomElement(['success', 'failed', 'pending']),
        ];
    }
}
