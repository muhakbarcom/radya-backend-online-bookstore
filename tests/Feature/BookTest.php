<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_book()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        $response = $this->postJson('/api/books', [
            'title' => 'New Book',
            'author' => 'Author Name',
            'genre' => 'Genre',
            'price' => 100,
            'stock' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Book added successfully',
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'New Book',
        ]);
    }

    public function test_admin_can_update_book()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum');

        $book = Book::factory()->create();

        $response = $this->putJson("/api/books/{$book->id}", [
            'title' => 'Updated Book Title',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Book updated successfully',
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Book Title',
        ]);
    }

    public function test_user_can_get_books()
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'genre', 'price', 'stock'],
                ],
            ]);
    }
}
