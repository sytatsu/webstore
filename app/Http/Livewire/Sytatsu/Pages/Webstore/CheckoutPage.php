<?php

declare(strict_types=1);

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Services\CartService;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\CheckoutService;
use Lunar\Stripe\Enums\CancellationReason;
use Lunar\Stripe\Facades\Stripe as StripeFacade;

class CheckoutPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.checkout';
    protected string $layout = 'layouts.checkout-layout';
    protected ?string $title = 'Checkout';

    private CartService $cartService;
    private CheckoutService $checkoutService;
    public array $lines = [];

    protected string $paymentType = 'stripe';
    public ?string $payment_intent = null;
    public ?string $payment_intent_client_secret = null;
    public ?string $redirect_status = null;

    protected $queryString = [
        'payment_intent',
        'payment_intent_client_secret',
        'redirect_status'
    ];

    public function boot(
        CartService $cartService,
        CheckoutService $checkoutService,
    ): void {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function mount()
    {
        if ($this->payment_intent) {
            $payment = $this->checkoutService->authorizePaymentIntent(
                paymentType: $this->paymentType,
                paymentIntent: $this->payment_intent,
                paymentIntentClientSecret: $this->payment_intent_client_secret,
            );

            if ($payment->success) {
                return redirect()->route('sytatsu.webstore.checkout.success', ['order_id' => $this->cart->orders()->first()->id]);
            }
        }

        $this->mapLines();
    }

    public function getCartProperty()
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
}
