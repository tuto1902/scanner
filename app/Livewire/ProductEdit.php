<?php

namespace App\Livewire;

use App\Services\InventoryApiClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProductEdit extends Component
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

    public string $error = '';

    public string $success = '';

    public bool $loading = false;

    public function mount(int $id): void
    {
        $this->productId = $id;
        $this->loadProduct();
    }

    public function loadProduct(): void
    {
        $this->loading = true;
        $this->error = '';

        try {
            // Check authentication first
            $token = session('auth_token');
            if (! $token) {
                $this->redirect('/login', navigate: true);

                return;
            }

            // Check if we have product data in session (from barcode scan)
            $sessionKey = "product_data_{$this->productId}";
            $sessionProductData = session($sessionKey);

            if ($sessionProductData) {
                // Use session data and clear it
                $this->product = $sessionProductData;
                session()->forget($sessionKey);

                $this->populateFormFields();
            } else {
                // Fallback to API call for direct URL access
                $apiClient = app(InventoryApiClient::class);
                $response = $apiClient->getProductById($this->productId, $token);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->product = $data['data'];

                    $this->populateFormFields();
                } else {
                    $this->error = 'Product not found.';
                }
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to load product. Please try again.';
        } finally {
            $this->loading = false;
        }
    }

    private function populateFormFields(): void
    {
        $this->name = $this->product['name'];
        $this->sku = $this->product['sku'];
        $this->description = $this->product['description'] ?? '';
        $this->price = $this->product['price'];
        $this->stock_quantity = $this->product['stock_quantity'];
        $this->suppliers = $this->product['suppliers']['data'] ?? [];
    }

    public function updateProduct(): void
    {
        $this->validate();

        $this->loading = true;
        $this->error = '';
        $this->success = '';

        try {
            $token = session('auth_token');
            if (! $token) {
                $this->redirect('/login', navigate: true);

                return;
            }

            $apiClient = app(InventoryApiClient::class);
            $response = $apiClient->updateProduct($this->productId, [
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'stock_quantity' => $this->stock_quantity,
            ], $token);

            if ($response->successful()) {
                $data = $response->json();
                $this->product = $data['data'];
                $this->success = 'Product updated successfully!';

                $this->populateFormFields();
            } else {
                $this->error = 'Unable to update product. Please try again.';
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to connect to server. Please try again.';
        } finally {
            $this->loading = false;
        }
    }

    public function goBack(): void
    {
        $this->redirect('/', navigate: true);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.product-edit');
    }
}
