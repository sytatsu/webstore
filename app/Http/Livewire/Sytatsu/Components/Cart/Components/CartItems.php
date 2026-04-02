<?php

namespace App\Http\Livewire\Sytatsu\Components\Cart\Components;

use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Models\Cart as LunarCart;

class CartItems extends Component
{
    private readonly CartService $cartService;

    public bool $checkout = false;
    public array $lines = [];

    protected $listeners
        = [
            'cart-updated' => 'mapLines',
            'shipping-option-updated' => 'mapLines',
        ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(bool $checkout = false): void
    {
        $this->checkout = $checkout;
        $this->mapLines();
    }

    public function rules(): array
    {
        return [
            'lines.*.quantity' => 'required|numeric',
        ];
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

    public function incrementLine(string $index): void
    {
        if ($this->isCartDisabled()) {
            return;
        }

        $this->lines = $this->cartService->incrementLine($this->lines, $index);
        $this->dispatch('cart-updated');
    }

    public function decrementLine(string $index): void
    {
        if ($this->isCartDisabled()) {
            return;
        }

        $this->lines = $this->cartService->decrementLine($this->lines, $index);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(string $index, int $quantity): void
    {
        if ($this->isCartDisabled()) {
            return;
        }

        $this->lines = $this->cartService->updateQuantity($this->lines, $index, $quantity);
        $this->validate();
        $this->dispatch('cart-updated');
    }

    public function removeLine($id): void
    {
        $this->cartService->removeLine($id);
        $this->dispatch('cart-updated');
    }

    public function updateLines(): void
    {
        $this->validate();
        $this->lines = $this->cartService->updateLines($this->lines);
        $this->dispatch('cart-updated');
    }

    public function isCartDisabled(): bool
    {
        return $this->checkout || ($this->cartService->isCartDisabled() ?? false);
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart.components.items');
    }
}
