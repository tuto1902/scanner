<?php

namespace App\Livewire;

use App\Services\InventoryApiClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public string $error = '';
    public bool $loading = false;

    public function login(InventoryApiClient $apiClient): void
    {
        $this->validate();

        $this->loading = true;
        $this->error = '';

        try {
            $response = $apiClient->login(
                $this->email,
                $this->password,
                'Scanner App'
            );

            if ($response->successful()) {
                $token = $response->body();
                
                session(['auth_token' => $token]);
                $this->dispatch('auth-token-received', token: $token);
                $this->redirect('/', navigate: true);
            } else {
                $this->error = 'Invalid credentials. Please try again.';
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to connect to the server. Please try again.';
        } finally {
            $this->loading = false;
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.login');
    }
}
