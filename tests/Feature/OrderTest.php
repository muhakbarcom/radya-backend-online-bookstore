<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // login as customer
        $customer = User::factory()->create()->assignRole('customer');
        $this->actingAs($customer, 'sanctum');
    }

    public function test_customer_can_place_order()
    {
        // create 5 books
        Book::factory(5)->create();

        $books = $this->getJson(route('books.index'))->json('data');

        $this->postJson(route('cart.add'), [
            'book_id' => $books[0]['id'],
            'quantity' => 1,
        ]);

        $this->postJson(route('cart.add'), [
            'book_id' => $books[1]['id'],
            'quantity' => 2,
        ]);

        $this->postJson(route('cart.add'), [
            'book_id' => $books[2]['id'],
            'quantity' => 3,
        ]);

        $this->postJson(route('cart.add'), [
            'book_id' => $books[3]['id'],
            'quantity' => 4,
        ]);

        $this->postJson(route('cart.add'), [
            'book_id' => $books[4]['id'],
            'quantity' => 5,
        ]);



        $response = $this->postJson(route('orders.place'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'total_price',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => auth()->id(),
            'total_price' => round(
                ($books[0]['price'] * 1) +
                    ($books[1]['price'] * 2) +
                    ($books[2]['price'] * 3) +
                    ($books[3]['price'] * 4) +
                    ($books[4]['price'] * 5),
                2
            ),
            'status' => 'completed',
        ]);
    }

    public function test_customer_cannot_place_order_if_cart_is_empty()
    {
        $response = $this->postJson(route('orders.place'));

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
            ]);

        $this->assertDatabaseCount('orders', 0);
    }
}
