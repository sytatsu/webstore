<?php


namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Services\CartService;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class CheckoutPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.checkout';
    protected ?string $title = 'Checkout';

    private CartService $cartService;
    public array $lines = [];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        $this->mapLines();
    }

    public function getCartProperty()
    {
        return $this->cartService->getCurrentCart();
    }

    public function getLinesProperty(): array
    {
        $this->mapLines();
        return $this->lines;
    }

    public function mapLines(): void
    {
        $this->lines = $this->cartService->mapCartLines();
    }
}
