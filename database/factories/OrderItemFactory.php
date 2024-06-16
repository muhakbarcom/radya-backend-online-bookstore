<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'book_id' => Book::factory(),
            'quantity' => fake()->numberBetween(1, 10),
            'price' => fake()->randomFloat(2, 5, 100),
        ];
    }
}
