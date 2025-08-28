<?php

namespace App\Livewire;

use App\Services\InventoryApiClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class Home extends Component
{
    public string $scannedBarcode = '';

    public string $error = '';

    #[On('barcode-scanned')]
    public function barcodeScanned($text)
    {
        $this->scannedBarcode = $text;
        $this->searchByBarcode(app(InventoryApiClient::class));
    }

    public function searchByBarcode(InventoryApiClient $apiClient): void
    {
        if (empty($this->scannedBarcode)) {
            $this->error = 'Please scan a barcode first.';

            return;
        }

        $this->error = '';

        try {
            $token = session('auth_token');
            if (! $token) {
                $this->redirect('/login', navigate: true);

                return;
            }

            $response = $apiClient->searchProductByBarcode($this->scannedBarcode, $token);

            if ($response->successful()) {
                $data = $response->json();
                $productData = $data['data'];
                $productId = $productData['id'];

                // Store product data in session to avoid duplicate API call
                session()->put("product_data_{$productId}", $productData);

                $this->redirect("/product/{$productId}/edit", navigate: true);
            } else {
                $this->error = 'Product not found. Please try another barcode.';
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to search product. Please try again.';
        }
    }

    public function goToSearch(): void
    {
        $this->redirect('/search', navigate: true);
    }

    public function logout(): void
    {
        session()->forget('auth_token');
        $this->dispatch('logout');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.home');
    }
}
