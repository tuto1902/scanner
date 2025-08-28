<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class InventoryApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://inventory-tracker.test/api';
    }

    public function login(string $email, string $password, string $deviceName): Response
    {
        return Http::post("{$this->baseUrl}/auth/login", [
            'email' => $email,
            'password' => $password,
            'device_name' => $deviceName,
        ]);
    }

    public function searchProductByBarcode(string $barcode, string $token): Response
    {
        return Http::withToken($token)
            ->post("{$this->baseUrl}/product/search", [
                'barcode' => $barcode,
            ]);
    }

    public function searchProductByName(string $name, string $token): Response
    {
        return Http::withToken($token)
            ->post("{$this->baseUrl}/product/by-name", [
                'name' => $name,
            ]);
    }

    public function getProductById(int $productId, string $token): Response
    {
        return Http::withToken($token)
            ->get("{$this->baseUrl}/product/by-id/{$productId}");
    }

    public function updateProduct(int $productId, array $data, string $token): Response
    {
        return Http::withToken($token)
            ->put("{$this->baseUrl}/product/{$productId}", $data);
    }
}
