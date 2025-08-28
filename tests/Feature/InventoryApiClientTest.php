<?php

use App\Services\InventoryApiClient;
use Illuminate\Support\Facades\Http;

test('can get product by id', function () {
    $responseData = [
        'data' => [
            'id' => 123,
            'name' => 'Test Product',
            'sku' => 'TEST123',
            'description' => 'A test product',
            'price' => 19.99,
            'stock_quantity' => 100,
        ],
    ];

    Http::fake([
        'http://inventory-tracker.test/api/product/by-id/123' => Http::response($responseData, 200),
    ]);

    $client = new InventoryApiClient();
    $response = $client->getProductById(123, 'test-token');

    expect($response->successful())->toBeTrue();
    expect($response->json())->toBe($responseData);
    
    Http::assertSent(function ($request) {
        return $request->url() === 'http://inventory-tracker.test/api/product/by-id/123'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token');
    });
});

test('handles failed product by id request', function () {
    Http::fake([
        'http://inventory-tracker.test/api/product/by-id/999' => Http::response(['error' => 'Not found'], 404),
    ]);

    $client = new InventoryApiClient();
    $response = $client->getProductById(999, 'test-token');

    expect($response->successful())->toBeFalse();
    expect($response->status())->toBe(404);
});
