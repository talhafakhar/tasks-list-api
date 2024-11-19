<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegister()
    {
        $response = $this->post('/api/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'username' => 'johndoe',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['user', 'token']);
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

    public function testLogin()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password'),
            'username' => 'johndoe',
        ]);

        $response = $this->post('/api/login', [
            'login' => 'johndoe@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    }

    public function testLogout()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->post('/api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Logged out successfully!'
        ]);
    }

    public function testSendPasswordResetLink()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
        ]);

        $response = $this->post('/api/forgot-password', [
            'email' => 'johndoe@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'We have emailed your password reset link.']);
    }

    public function testResetPassword()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/api/reset-password', [
            'token' => $token,
            'email' => 'johndoe@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'Your password has been reset.']);
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }


    public function testRegisterWithInvalidData()
    {
        $response = $this->postJson('/api/register', [
            'name' => '', // Invalid data: name is empty
            'email' => 'not-an-email', // Invalid email format
            'password' => 'short', // Invalid data: password too short
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422); // Standard Laravel validation failure status
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function testLoginWithInvalidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'user@example.com',
            'password' => 'wrongpassword', // Incorrect password
        ]);

        $response->assertStatus(406);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Provided credentials are invalid!',
        ]);
    }

    public function testLogoutWithoutAuthenticatedUser()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401); // Unauthorized due to no user being authenticated
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function testSendPasswordResetLinkWithInvalidEmail()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'not-valid-email', // Invalid email format
        ]);

        $response->assertStatus(422); // Standard Laravel validation failure status
        $response->assertJsonValidationErrors(['email']);
    }

    public function testResetPasswordWithInvalidToken()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);
        $invalidToken = 'invalid-token'; // Simulating an invalid or expired token scenario

        $response = $this->postJson('/api/reset-password', [
            'token' => $invalidToken,
            'email' => 'user@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(406);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Link is invalid or expired!',
        ]);
    }
}

