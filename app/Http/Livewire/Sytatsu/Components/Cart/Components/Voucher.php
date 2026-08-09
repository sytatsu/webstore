<?php

namespace App\Http\Livewire\Sytatsu\Components\Cart\Components;

use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Facades\Discounts;
use Lunar\Models\Cart as LunarCart;

class Voucher extends Component
{
    private CartService $cartService;

    public string $code = '';

    protected $listeners = [
        'cart-updated' => '$refresh',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function getCartProperty(): LunarCart
    {
        return $this->cartService->getCurrentCart();
    }

    public function apply(): void
    {
        $this->resetErrorBag();

        $code = trim($this->code);

        if ($code === '') {
            return;
        }

        if (! Discounts::validateCoupon($code)) {
            $this->addError('code', __('This discount code is invalid or has expired.'));
            return;
        }

        $cart = $this->cart;
        $cart->coupon_code = $code;
        $cart->save();
        $cart->refresh()->calculate();

        if ($cart->discounts?->isEmpty()) {
            $cart->coupon_code = null;
            $cart->save();

            $this->addError('code', __('This discount code does not apply to your order.'));
            return;
        }

        $this->code = '';

        $this->dispatch('cart-updated');
    }

    public function remove(): void
    {
        $cart = $this->cart;
        $cart->coupon_code = null;
        $cart->save();
        $cart->refresh()->calculate();

        $this->dispatch('cart-updated');
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.cart.components.voucher');
    }
}
