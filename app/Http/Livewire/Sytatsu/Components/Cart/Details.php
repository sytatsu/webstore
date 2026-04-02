<?php

namespace App\Http\Livewire\Sytatsu\Components\Cart;

use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\DataTypes\ShippingOption;
use Lunar\Models\Cart as LunarCart;

class Details extends Component
{

    private readonly CartService $cartService;

    public bool $checkout = false;
    public array $lines = [];
    protected ?ShippingOption $shippingOption;

    protected $listeners
        = [
            'cart-updated' => 'mapLines',
            'shipping-option-updated' => 'mapLines',
        ];

    public function boot(CartService $cartService): void
    {
        $this->cartService  = $cartService;
    }

    public function mount(bool $checkout = false): void
    {
        $this->checkout = $checkout;
        $this->mapLines();
    }

    public function getCartProperty(): LunarCart
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
        $this->shippingOption = $this->cartService->getShippingOption();
    }

    public function isCartDisabled(): bool
    {
        return $this->checkout || ($this->cartService->isCartDisabled() ?? false);
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart.details');
    }
}
