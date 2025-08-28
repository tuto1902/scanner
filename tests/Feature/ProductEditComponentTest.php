<?php

use App\Livewire\ProductEdit;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    // Mock successful authentication
    session(['auth_token' => 'test-token']);
});

test('product edit component uses session data when available', function () {
    $productData = [
        'id' => 123,
        'name' => 'Test Product',
        'sku' => 'TEST123',
        'description' => 'A test product',
        'price' => 19.99,
        'stock_quantity' => 100,
        'suppliers' => ['data' => []],
    ];

    // Store product data in session (simulating barcode scan flow)
    session(['product_data_123' => $productData]);

    Livewire::test(ProductEdit::class, ['id' => 123])
        ->assertSet('name', 'Test Product')
        ->assertSet('sku', 'TEST123')
        ->assertSet('description', 'A test product')
        ->assertSet('price', 19.99)
        ->assertSet('stock_quantity', 100)
        ->assertSet('loading', false);

    // Verify session data was cleared
    expect(session('product_data_123'))->toBeNull();
});

test('product edit component falls back to API call when no session data', function () {
    $responseData = [
        'data' => [
            'id' => 456,
            'name' => 'API Product',
            'sku' => 'API456',
            'description' => 'From API',
            'price' => 29.99,
            'stock_quantity' => 50,
            'suppliers' => ['data' => []],
        ],
    ];

    Http::fake([
        '*/product/by-id/456' => Http::response($responseData, 200),
    ]);

    Livewire::test(ProductEdit::class, ['id' => 456])
        ->assertSet('name', 'API Product')
        ->assertSet('sku', 'API456')
        ->assertSet('description', 'From API')
        ->assertSet('price', 29.99)
        ->assertSet('stock_quantity', 50)
        ->assertSet('loading', false);
});

test('product edit component redirects to login when not authenticated', function () {
    // Clear auth token
    session()->forget('auth_token');

    Livewire::test(ProductEdit::class, ['id' => 789])
        ->assertRedirect('/login');
});
