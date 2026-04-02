<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class Cart extends Component
{
    private readonly CartService $cartService;

    public array $lines = [];
    public bool $cartOpen = false;
    public int $cartTotalQuantity = 0;

    protected $listeners = [
        'cart-updated' => 'cartUpdated',
        'add-to-cart' => 'openCart',
        'shipping-option-updated' => 'cartUpdated',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        $this->calculateCartTotalQuantity();

        if ($this->isCartDisabled()) {
            $this->cartOpen = false;
        }
    }

    public function openCart(): void
    {
        if ($this->isCartDisabled()) {
            return;
        }

        $this->cartOpen = true;
    }

    public function isCartDisabled(): bool
    {
        return $this->cartService->isCartDisabled();
    }

    public function closeCart(): void
    {
        $this->cartOpen = false;
    }

    public function cartUpdated(): void
    {
        $this->setShippingOption();
        $this->calculateCartTotalQuantity();

        if ($this->isCartDisabled()) {
            $this->cartOpen = false;
        }
    }

    public function setShippingOption (): void
    {
        if ($this->cartService->getCurrentCart()->shippingAddress) {
            $this->cartService->getCurrentCart()->setShippingOption($this->cartService->recalculateShippingOption($this->cartService->getCurrentCart()));
        }
    }

    public function calculateCartTotalQuantity(): void
    {
        $this->cartTotalQuantity = $this->cartService->getTotalQuantity();
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart');
    }
}
