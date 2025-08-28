<?php

namespace App\Livewire\Forms;

use App\Services\InventoryApiClient;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public int $productId;

    public array $product = [];

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $sku = '';

    #[Validate('nullable|string')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public float $price = 0;

    #[Validate('required|integer|min:0')]
    public int $stock_quantity = 0;

    public array $suppliers = [];

    public function fillFromProduct(array $productData): void
    {
        $this->product = $productData;
        $this->productId = $productData['id'];
        $this->name = $productData['name'];
        $this->sku = $productData['sku'];
        $this->description = $productData['description'] ?? '';
        $this->price = $productData['price'];
        $this->stock_quantity = $productData['stock_quantity'];
        $this->suppliers = $productData['suppliers']['data'] ?? [];
    }

    public function update(string $token): bool
    {
        $this->validate();

        $apiClient = app(InventoryApiClient::class);
        $response = $apiClient->updateProduct($this->productId, [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
        ], $token);

        return $response->successful();
    }
}