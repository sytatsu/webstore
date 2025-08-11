<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart as LunarCart;
use Lunar\Models\CartLine;
use Lunar\Models\ProductOption;

class Cart extends Component
{
    private readonly CartService $cartService;

    public array $lines = [];
    public bool $cartOpen = false;

    protected $listeners = [
        'add-to-cart' => 'handleAddToCart',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
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

    public function getCartTotalQuantityProperty(): int
    {
        return $this->cartService->getTotalQuantity($this->lines);
    }

    public function rules(): array
    {
        return [
            'lines.*.quantity' => 'required|numeric',
        ];
    }

    public function incrementLine(string $index): void
    {
        $this->lines = $this->cartService->incrementLine($this->lines, $index);
        $this->dispatch('cart-updated');
    }

    public function decrementLine(string $index): void
    {
        $this->lines = $this->cartService->decrementLine($this->lines, $index);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(string $index, int $quantity): void
    {
        $this->lines = $this->cartService->updateQuantity($this->lines, $index, $quantity);
        $this->validate();
        $this->dispatch('cart-updated');
    }

    public function updateLines(): void
    {
        $this->validate();
        $this->lines = $this->cartService->updateLines($this->lines);
        $this->dispatch('cart-updated');
    }

    public function removeLine($id): void
    {
        $this->cartService->removeLine($id);
        $this->mapLines();
        $this->dispatch('cart-updated');
    }

    public function handleAddToCart(): void
    {
        $this->mapLines();
        $this->cartOpen = true;
    }

    public function mapLines(): void
    {
        $this->lines = $this->cartService->mapCartLines();
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart');
    }
}
