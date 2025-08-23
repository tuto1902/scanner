<?php

namespace App\Livewire;

use App\Services\InventoryApiClient;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductSearch extends Component
{
    public string $search = '';
    public array $products = [];
    public string $error = '';
    public bool $loading = false;

    public function updatedSearch(): void
    {
        if (strlen($this->search) >= 2) {
            $this->searchProducts();
        } else {
            $this->products = [];
        }
    }

    public function searchProducts(): void
    {
        $this->loading = true;
        $this->error = '';

        try {
            $token = session('auth_token');
            if (!$token) {
                $this->redirect('/login', navigate: true);
                return;
            }

            $apiClient = app(InventoryApiClient::class);
            $response = $apiClient->searchProductByName($this->search, $token);

            if ($response->successful()) {
                $data = $response->json();
                $this->products = $data['data'] ?? [];
            } else {
                $this->error = 'Unable to search products. Please try again.';
                $this->products = [];
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to connect to server. Please try again.';
            $this->products = [];
        } finally {
            $this->loading = false;
        }
    }

    public function selectProduct(int $productId): void
    {
        $this->redirect("/product/{$productId}/edit", navigate: true);
    }

    public function goBack(): void
    {
        $this->redirect('/', navigate: true);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.product-search');
    }
}
