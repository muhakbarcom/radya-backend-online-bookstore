<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(route('register'), $data);

        // 1. Assert the status code
        $response->assertStatus(201);

        // 2. Assert the response structure
        $response->assertJsonStructure([
            'isSuccess',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);

        // 3. isSuccessful should be true
        $response->assertJson([
            'isSuccess' => true,
        ]);

        // 4. Assert the response data
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $response = $this->postJson(route('register'), $data);

        // 1. Status code should be 500
        $response->assertStatus(500);

        // 2. Assert the response structure
        $response->assertJsonStructure([
            'isSuccess',
            'message',
            'data',
        ]);

        // 3. isSuccessful should be false
        $response->assertJson([
            'isSuccess' => false,
        ]);
    }


    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data' => [
                    'user',
                    'token',
                ],
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data',
            ]);
    }


    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->actingAs($user)->postJson(route('logout'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'isSuccess',
                'message',
                'data'
            ]);
    }
}
