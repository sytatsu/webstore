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

class CartStaticOverview extends Component
{

    private readonly CartService $cartService;

    protected array $lines = [];
    protected ?ShippingOption $shippingOption;

    protected $listeners = [
        'updated-cart' => 'mapLines',
        'updated-shipping-option' => 'mapLines',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService  = $cartService;
    }

    public function mount(): void
    {
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
    }

    public function handleUpdatedCart(): void
    {
        $this->mapLines();
    }

    public function handleUpdatedShippingOption(): void
    {
        $this->shippingOption = $this->cartService->getShippingOption();
    }

    public function getShippingOption(): ShippingOption
    {
        return $this->shippingOption;
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart.cart-details');
    }
}
