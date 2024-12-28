<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'token',
                'user'
            ]);
    }

    public function test_it_cant_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }

    public function test_it_can_register_a_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John',
            'email' => 'example@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'token',
                'user'
            ]);
    }

    public function test_register_validation_error_on_invalid_dat()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John',
            'email' => 'example@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password1',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function test_forgot_password()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Confirmation OTP has been sent to your email',
            ]);
    }

    public function test_otp_verification()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $otp = '123456';
        \DB::table('password_reset_otps')->insert([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/otp-verification', [
            'email' => $user->email,
            'otp' => $otp
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'OTP verified.',
            ]);
    }

    public function test_expired_otp()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $otp = '123456';
        \DB::table('password_reset_otps')->insert([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(-1),
        ]);

        $response = $this->postJson('/api/otp-verification', [
            'email' => $user->email,
            'otp' => $otp
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid or expired OTP.',
            ]);
    }

    public function test_reset_password()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $otp = '123456';
        \DB::table('password_reset_otps')->insert([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'otp' => $otp,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password reset successfully.',
            ]);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }
}
