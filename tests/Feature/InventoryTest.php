<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    // setup
    public function setUp(): void
    {
        parent::setUp();

        // prepare the roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // create 10 books
        Book::factory(10)->create();
    }

    public function test_admin_can_see_inventory()
    {
        $admin = User::factory()->create()->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        $response = $this->getJson(route('inventory.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'quantity',
                    ],
                ],
            ]);
    }

    public function test_admin_can_add_quantity()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        // get last book
        $book = Book::latest()->first();
        $oldQuantity = $book->quantity;

        $response = $this->postJson(route('inventory.add-stock', $book->id), [
            'quantity' => 10,
        ]);


        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    'id',
                    'title',
                    'author',
                    'quantity',
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'quantity' => $oldQuantity + 10,
        ]);
    }

    public function test_admin_can_reduce_quantity()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        $book = Book::factory(1)->create(
            ['quantity' => 10]
        );
        $book = $book->last();
        $oldQuantity = $book->quantity;

        $response = $this->postJson(route('inventory.reduce-stock', $book->id), [
            'quantity' => 5,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    'id',
                    'title',
                    'author',
                    'quantity',
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'quantity' => $oldQuantity - 5,
        ]);
    }

    public function test_admin_can_delete_book()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        $book = Book::latest()->first();

        $response = $this->deleteJson(route('inventory.delete', $book->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data'
            ]);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
