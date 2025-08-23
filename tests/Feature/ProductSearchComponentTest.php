<?php

use App\Livewire\ProductSearch;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('product search component renders correctly', function () {
    session(['auth_token' => 'test-token']);
    
    Livewire::test(ProductSearch::class)
        ->assertSee('Search Products')
        ->assertSee('Product Name')
        ->assertSee('Type product name...');
});

test('product search shows message for short search terms', function () {
    session(['auth_token' => 'test-token']);
    
    Livewire::test(ProductSearch::class)
        ->set('search', 'a')
        ->assertSee('Type at least 2 characters to search');
});

test('product search calls API and displays results', function () {
    session(['auth_token' => 'test-token']);
    
    Http::fake([
        'inventory-tracker.test/api/product/by-name' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Test Product',
                    'sku' => 'TEST-001',
                    'description' => 'A test product',
                    'price' => 10.99,
                    'stock_quantity' => 50
                ]
            ]
        ], 200),
    ]);

    Livewire::test(ProductSearch::class)
        ->set('search', 'test')
        ->assertSee('Test Product')
        ->assertSee('TEST-001')
        ->assertSee('$10.99')
        ->assertSee('Stock: 50');
});

test('product search handles no results', function () {
    session(['auth_token' => 'test-token']);
    
    Http::fake([
        'inventory-tracker.test/api/product/by-name' => Http::response([
            'data' => []
        ], 200),
    ]);

    Livewire::test(ProductSearch::class)
        ->set('search', 'nonexistent')
        ->assertSee('No products found for', false);
});

test('product search redirects when selecting product', function () {
    session(['auth_token' => 'test-token']);
    
    Livewire::test(ProductSearch::class)
        ->call('selectProduct', 1)
        ->assertRedirect('/product/1/edit');
});
