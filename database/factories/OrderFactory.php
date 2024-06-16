<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['completed']),
            'total_price' => fake()->randomFloat(2, 20, 500),
            'order_number' => strtoupper(fake()->unique()->bothify('ORDER-#######')),
        ];
    }
}
