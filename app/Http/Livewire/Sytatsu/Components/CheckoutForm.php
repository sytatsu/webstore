<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class CheckoutForm extends Component
{

    private CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.checkout-form');
    }
}
