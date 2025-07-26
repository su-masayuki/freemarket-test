<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials()
    {
        $email = 'testuser' . uniqid() . '@example.com';

        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $email = 'testuser' . uniqid() . '@example.com';

        User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    public function test_login_requires_email()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
    public function test_login_requires_password()
    {
        $email = 'testuser' . uniqid() . '@example.com';

        $response = $this->post('/login', [
            'email' => $email,
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
    public function test_login_with_unregistered_credentials_shows_validation_message()
    {
        $response = $this->post('/login', [
            'email' => 'unregistered@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}