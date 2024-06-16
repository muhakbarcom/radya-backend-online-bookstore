<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShopingCartTest extends TestCase
{
    use RefreshDatabase;

    // setup, login as customer
    public function setUp(): void
    {
        parent::setUp();

        // prepare the roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // create a customer
        $customer = User::factory()->create()->assignRole('customer');
        $this->actingAs($customer, 'sanctum');

        // create 10 books
        Book::factory(10)->create();
    }

    public function test_customer_can_add_book_to_cart()
    {
        $book = Book::first();

        $response = $this->postJson(route('cart.add'), [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    'id',
                    'book_id',
                    'quantity',
                ],
            ]);

        $this->assertDatabaseHas('shopping_carts', [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);
    }

    public function test_customer_cannot_add_book_to_cart_with_invalid_data()
    {
        $response = $this->postJson(route('cart.add'), [
            'book_id' => 100,
            'quantity' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
            ]);

        // json validation error
        $response->assertJsonValidationErrors(['book_id', 'quantity']);


        $this->assertDatabaseCount('shopping_carts', 0);
    }

    public function test_customer_can_update_book_in_cart()
    {
        $book = Book::first();

        $this->postJson(route('cart.add'), [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);

        $response = $this->putJson(route('cart.update', 1), [
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    'id',
                    'book_id',
                    'quantity',
                ],
            ]);

        $this->assertDatabaseHas('shopping_carts', [
            'book_id' => $book->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_update_book_in_cart_with_invalid_data()
    {
        $book = Book::first();

        $this->postJson(route('cart.add'), [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);

        $response = $this->putJson(route('cart.update', 1), [
            'quantity' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
            ]);

        // json validation error
        $response->assertJsonValidationErrors(['quantity']);

        $this->assertDatabaseHas('shopping_carts', [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);
    }

    public function test_customer_can_remove_book_from_cart()
    {
        $book = Book::first();

        $this->postJson(route('cart.add'), [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);

        $response = $this->deleteJson(route('cart.remove', 1));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data',
            ]);

        $this->assertDatabaseCount('shopping_carts', 0);
    }

    public function test_customer_can_view_cart()
    {
        $book = Book::first();

        $this->postJson(route('cart.add'), [
            'book_id' => $book->id,
            'quantity' => 1,
        ]);

        $response = $this->getJson(route('cart.view'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'book_id',
                        'quantity',
                    ],
                ],
            ]);
    }
}
