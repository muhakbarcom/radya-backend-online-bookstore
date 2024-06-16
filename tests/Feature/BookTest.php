<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    // before each test, we will create a admin and a customer role
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    public function test_admin_can_add_book()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        $response = $this->postJson(route('books.store'), [
            'title' => 'New Book',
            'author' => 'Author Name',
            'genre' => 'Genre',
            'price' => 100,
            'quantity' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    'id',
                    'title',
                    'author',
                    'genre',
                    'price',
                    'quantity',
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'New Book',
        ]);
    }

    public function test_admin_cannot_add_book_with_invalid_data()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        $response = $this->postJson(route('books.store'), [
            'title' => '',
            'author' => '',
            'genre' => '',
            'price' => '',
            'quantity' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonValidationErrors([
                'title',
                'author',
                'genre',
                'price',
                'quantity',
            ]);
    }

    public function test_admin_can_update_book()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        Book::factory(1)->create();

        $bookId = Book::first()->id;

        $response = $this->putJson(route('books.update', $bookId), [
            'title' => 'Updated Book',
            'author' => 'Author Name',
            'genre' => 'Genre',
            'price' => 100,
            'quantity' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data'
            ]);

        // isSuccess should be true
        $this->assertTrue($response['isSuccess']);

        $this->assertDatabaseHas('books', [
            'title' => 'Updated Book',
        ]);
    }

    public function test_admin_cannot_update_book_with_invalid_data()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        $book = Book::factory()->create();

        $response = $this->putJson(route('books.update', $book->id), [
            'title' => '',
            'author' => '',
            'genre' => '',
            'price' => '',
            'quantity' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonValidationErrors([
                'title',
                'author',
                'genre',
                'price',
                'quantity',
            ]);
    }

    public function test_user_can_get_books()
    {
        // create a customer
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer, 'sanctum');

        Book::factory()->count(5)->create();

        $response = $this->getJson(route('books.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'genre',
                        'price',
                        'quantity',
                    ],
                ],
            ]);

        $this->assertCount(5, $response['data']);
    }

    public function test_user_can_get_single_book()
    {
        // create a admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        $book = Book::factory()->create();

        $response = $this->getJson(route('books.show', $book->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data'
            ]);

        $this->assertTrue($response['isSuccess']);
    }

    public function test_user_can_filter_books_by_genre()
    {
        // create a customer
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer, 'sanctum');

        Book::factory()->create([
            'genre' => 'Fiction',
        ]);

        Book::factory()->create([
            'genre' => 'Non-Fiction',
        ]);

        $response = $this->getJson(route('books.index', ['genre' => 'Fiction']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'genre',
                        'price',
                        'quantity',
                    ],
                ],
            ]);

        $this->assertCount(1, $response['data']);
    }

    public function test_user_can_filter_books_by_author()
    {
        // create a customer
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer, 'sanctum');

        Book::factory()->create([
            'author' => 'Author 1',
        ]);

        Book::factory()->create([
            'author' => 'Author 2',
        ]);

        $response = $this->getJson(route('books.index', ['author' => 'Author 1']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'genre',
                        'price',
                        'quantity',
                    ],
                ],
            ]);

        $this->assertCount(1, $response['data']);
    }
}
