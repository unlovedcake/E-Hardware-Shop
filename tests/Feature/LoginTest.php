<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Act: Attempt to log in
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert: Check if the user is authenticated
        $response->assertRedirect('/guest'); // Or the intended URL after login
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_incorrect_password()
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Act: Attempt to log in with an incorrect password
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert: Check if the user is not authenticated
        $response->assertSessionHasErrors('email'); // Email is usually the field that receives the error message
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_non_existent_email()
    {
        // Act: Attempt to log in with a non-existent email
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        // Assert: Check if the user is not authenticated
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
