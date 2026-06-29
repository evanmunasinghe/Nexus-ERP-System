<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('welcome page shows the login form', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Administrative Login')
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false);
});

test('login route uses the merged welcome login page', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('Industrial Resource')
        ->assertSee('Administrative Login');
});

test('users can sign in from the welcome page login form', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login.submit'), [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});
