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
        ->assertSet('form.name', 'Test Product')
        ->assertSet('form.sku', 'TEST123')
        ->assertSet('form.description', 'A test product')
        ->assertSet('form.price', 19.99)
        ->assertSet('form.stock_quantity', 100);

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
        ->assertSet('form.name', 'API Product')
        ->assertSet('form.sku', 'API456')
        ->assertSet('form.description', 'From API')
        ->assertSet('form.price', 29.99)
        ->assertSet('form.stock_quantity', 50);
});

test('product edit component redirects to login when not authenticated', function () {
    // Clear auth token
    session()->forget('auth_token');

    Livewire::test(ProductEdit::class, ['id' => 789])
        ->assertRedirect('/login');
});

test('product edit component redirects to home after successful update', function () {
    $productData = [
        'id' => 123,
        'name' => 'Test Product',
        'sku' => 'TEST123',
        'description' => 'A test product',
        'price' => 19.99,
        'stock_quantity' => 100,
        'suppliers' => ['data' => []],
    ];

    $updatedProductData = [
        'data' => [
            'id' => 123,
            'name' => 'Updated Product',
            'sku' => 'TEST123',
            'description' => 'An updated product',
            'price' => 29.99,
            'stock_quantity' => 75,
            'suppliers' => ['data' => []],
        ],
    ];

    // Store product data in session
    session(['product_data_123' => $productData]);

    // Mock successful update API call
    Http::fake([
        '*/product/123' => Http::response($updatedProductData, 200),
    ]);

    Livewire::test(ProductEdit::class, ['id' => 123])
        ->set('form.name', 'Updated Product')
        ->set('form.description', 'An updated product')
        ->set('form.price', 29.99)
        ->set('form.stock_quantity', 75)
        ->call('updateProduct')
        ->assertRedirect('/');

    // Verify session flash message was set
    expect(session('status'))->toBe('Product updated successfully!');
});

test('product edit component shows error when update fails', function () {
    $productData = [
        'id' => 123,
        'name' => 'Test Product',
        'sku' => 'TEST123',
        'description' => 'A test product',
        'price' => 19.99,
        'stock_quantity' => 100,
        'suppliers' => ['data' => []],
    ];

    // Store product data in session
    session(['product_data_123' => $productData]);

    // Mock failed update API call
    Http::fake([
        '*/product/123' => Http::response(['error' => 'Update failed'], 400),
    ]);

    Livewire::test(ProductEdit::class, ['id' => 123])
        ->set('form.name', 'Updated Product')
        ->call('updateProduct')
        ->assertSet('error', 'Unable to update product. Please try again.')
        ->assertNoRedirect();
});
