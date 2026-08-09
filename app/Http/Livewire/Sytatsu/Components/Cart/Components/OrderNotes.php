<?php

namespace App\Http\Livewire\Sytatsu\Components\Cart\Components;

use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Models\Cart as LunarCart;

class OrderNotes extends Component
{
    private CartService $cartService;

    public string $notes = '';

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        $this->notes = (string) ($this->cart->meta['notes'] ?? '');
    }

    public function getCartProperty(): LunarCart
    {
        return $this->cartService->getCurrentCart();
    }

    public function updatedNotes(string $value): void
    {
        $meta = $this->cart->meta?->getArrayCopy() ?? [];
        $meta['notes'] = trim($value) !== '' ? $value : null;

        $this->cart->update(['meta' => $meta]);
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart.components.order-notes');
    }
}
