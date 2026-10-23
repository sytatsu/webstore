<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Services\CartService;
use Lunar\Models\Cart;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class CartPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.cart';
    public ?string $label;

    private readonly CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function getCartProperty(): Cart
    {
        return $this->cartService->getCurrentCart();
    }
}
