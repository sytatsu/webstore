<?php

namespace App\Http\Livewire\Sytatsu\Components\Checkout;

use App\Services\CartService;
use Livewire\Component;
use Lunar\Models\Cart;
use Lunar\Stripe\Enums\CancellationReason;
use Lunar\Stripe\Facades\Stripe as StripeFacade;
use Stripe\Stripe;

class PaymentForm extends Component
{
    public $returnUrl;
    public $policy;

    protected CartService $cartService;

    public string $clientSecret;

    protected $listeners = [
        'cart-updated' => 'refreshPayment'
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
        $this->clientSecret = StripeFacade::createIntent($this->cart)->client_secret;
    }

    public function refreshPayment(): void
    {
        $this->cart->refresh()->calculate();
        StripeFacade::syncIntent($this->cart);
    }

    public function getCartProperty(): Cart
    {
        return $this->cartService->getCurrentCart();
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
