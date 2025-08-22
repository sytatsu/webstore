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
        'add-to-cart' => 'openCart',
        'cart-updated' => 'calculateCartTotalQuantity'
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
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

    public function calculateCartTotalQuantity(): void
    {
        $this->cartTotalQuantity = $this->cartService->getTotalQuantity();
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart');
    }
}
