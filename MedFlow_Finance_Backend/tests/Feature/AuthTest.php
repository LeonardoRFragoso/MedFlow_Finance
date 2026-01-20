<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $clinic = Clinic::factory()->create();
        $user = User::factory()->create([
            'clinic_id' => $clinic->id,
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'clinic_id'],
                'token',
                'clinic' => ['id', 'name'],
            ]);

        $this->assertNotNull($response->json('token'));
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $clinic = Clinic::factory()->create();
        User::factory()->create([
            'clinic_id' => $clinic->id,
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $clinic = Clinic::factory()->create();
        $user = User::factory()->create(['clinic_id' => $clinic->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);
    }
}
