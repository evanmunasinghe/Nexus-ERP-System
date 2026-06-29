<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('authenticated users can create user accounts', function () {
    $admin = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    $response->assertRedirect(route('users.index'));

    $user = User::where('email', 'new-admin@example.com')->first();

    expect($user)->not->toBeNull();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('authenticated users can update user accounts', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated-user@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response->assertRedirect(route('users.index'));

    expect($user->refresh())
        ->name->toBe('Updated User')
        ->email->toBe('updated-user@example.com');
});

test('authenticated users can delete other user accounts', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->delete(route('users.destroy', $user));

    $response->assertRedirect(route('users.index'));

    $this->assertModelMissing($user);
});

test('authenticated users cannot delete their own account', function () {
    $admin = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->delete(route('users.destroy', $admin));

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors('user');

    $this->assertModelExists($admin);
});
