<?php

namespace Tests\Feature;

use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User Logged out',
            ]);
    }

    public function test_logged_in_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'email'
                ]
            ]);
    }

    public function test_logged_in_user_with_unauthorized_access()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken . "123";

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_user_preferences()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user/preferences');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function test_saving_user_preferences()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $source = Source::create(['name' => 'Demo']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/user/preferences', [
                'preferences' => [
                    [
                        'type' => 'source',
                        'value' => $source->id
                    ]
                ]
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message'
            ]);
    }
}
