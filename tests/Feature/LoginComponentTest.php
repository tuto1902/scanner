<?php

use App\Livewire\Login;
use App\Services\InventoryApiClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('login component renders correctly', function () {
    Livewire::test(Login::class)
        ->assertSee('Scanner App')
        ->assertSee('Sign in to your account')
        ->assertSee('Email address')
        ->assertSee('Password');
});

test('login component validates required fields', function () {
    Livewire::test(Login::class)
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});

test('login component validates email format', function () {
    Livewire::test(Login::class)
        ->set('email', 'invalid-email')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('successful login redirects to home', function () {
    Http::fake([
        'inventory-tracker.test/api/auth/login' => Http::response('test-token', 200),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'admin@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect('/');
        
    expect(session('auth_token'))->toBe('test-token');
});

test('failed login shows error message', function () {
    Http::fake([
        'inventory-tracker.test/api/auth/login' => Http::response('Unauthorized', 401),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'admin@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertSee('Invalid credentials. Please try again.');
});
