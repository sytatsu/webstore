<?php

namespace App\Http\Livewire\Sytatsu\Components\Checkout;

use App\Services\CartService;
use Livewire\Component;
use Lunar\Models\Cart;
use Lunar\Stripe\Facades\Stripe as StripeFacade;
use Stripe\Stripe;

class PaymentForm extends Component
{
    public $returnUrl;
    public $policy;

    protected CartService $cartService;

    protected $listeners = [
        'updated-cart' => 'refreshCart'
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        Stripe::setApiKey(config('services.stripe.key'));
        $this->policy = config('stripe.policy', 'capture');
        $this->returnUrl = route('sytatsu.webstore.checkout');
    }

    protected function refreshCart(): void
    {
        $this->cart->refresh();
    }

    public function getCartProperty(): Cart
    {
        return $this->cartService->getCurrentCart();
    }

    public function getClientSecretProperty(): ?string
    {
        $intent = StripeFacade::createIntent($this->cart);

        return $intent->client_secret;
    }

    public function getBillingProperty(): mixed
    {
        return $this->cart->billingAddress;
    }

    public function render()
    {
        return view('sytatsu.components.livewire.checkout.payment-form');
    }
}
