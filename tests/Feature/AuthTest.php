<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;


class AuthTest extends TestCase
{
    use RefreshDatabase;

   #[Test]
    public function it_can_register_a_user()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
    }

   #[Test]
    public function it_validates_registration_request()
    {
        $response = $this->postJson(route('auth.register'), []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

   #[Test]
    public function it_can_login_a_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

   #[Test]
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertSeeText('Invalid credentials');
    }

   #[Test]
   public function it_can_logout_a_user()
    {
        // Arrange
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        // Act
        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson(route('auth.logout'));

        // Assert response
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully.',
            ]);

        // Assert token is revoked (refresh user from DB)
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

}
