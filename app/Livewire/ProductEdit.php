<?php

namespace App\Livewire;

use App\Livewire\Forms\ProductForm;
use App\Services\InventoryApiClient;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductEdit extends Component
{
    public ProductForm $form;

    public int $productId;

    public string $error = '';

    public function mount(int $id): void
    {
        $this->productId = $id;
        $this->loadProduct();
    }

    public function loadProduct(): void
    {
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
                session()->forget($sessionKey);

                $this->form->fillFromProduct($sessionProductData);
            } else {
                // Fallback to API call for direct URL access
                $apiClient = app(InventoryApiClient::class);
                $response = $apiClient->getProductById($this->productId, $token);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->form->fillFromProduct($data['data']);
                } else {
                    $this->error = 'Product not found.';
                }
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to load product. Please try again.';
        }
    }


    public function updateProduct(): void
    {
        $this->error = '';

        try {
            $token = session('auth_token');
            if (! $token) {
                $this->redirect('/login', navigate: true);

                return;
            }

            if ($this->form->update($token)) {
                session()->flash('status', 'Product updated successfully!');
                $this->redirect('/', navigate: true);
            } else {
                $this->error = 'Unable to update product. Please try again.';
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to connect to server. Please try again.';
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
