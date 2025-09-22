<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Services\CartService;
use App\Services\ShippingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class Cart extends Component
{
    private readonly CartService $cartService;
    private readonly ShippingService $shippingService;

    public array $lines = [];
    public bool $cartOpen = false;
    public int $cartTotalQuantity = 0;

    protected $listeners = [
        'add-to-cart' => 'openCart',
        'cart-updated' => 'cartUpdated',
    ];

    public function boot(CartService $cartService, ShippingService $shippingService): void
    {
        $this->cartService = $cartService;
        $this->shippingService = $shippingService;
    }

    public function mount(): void
    {
        $this->calculateCartTotalQuantity();
    }

    public function openCart(): void
    {
        $this->cartOpen = true;
    }

    public function closeCart(): void
    {
        $this->cartOpen = false;
    }

    public function cartUpdated(): void
    {
        $this->setShippingOption();
        $this->calculateCartTotalQuantity();
    }

    public function setShippingOption (): void
    {
        if ($this->cartService->getCurrentCart()->shippingAddress) {
            $this->cartService->getCurrentCart()->setShippingOption($this->shippingService->recalculateShippingOption($this->cartService->getCurrentCart()));
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
