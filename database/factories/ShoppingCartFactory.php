<?php

namespace Database\Factories;

use App\Models\ShoppingCart;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingCartFactory extends Factory
{
    protected $model = ShoppingCart::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'quantity' => fake()->numberBetween(1, 10),
        ];
    }
}
