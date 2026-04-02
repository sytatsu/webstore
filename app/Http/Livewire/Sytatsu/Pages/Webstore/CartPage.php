<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class CartPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.cart';
    public ?string $label;

    private readonly \App\Services\CartService $cartService;

    public function boot(\App\Services\CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function getCartProperty(): \Lunar\Models\Cart
    {
        return $this->cartService->getCurrentCart();
    }
}
