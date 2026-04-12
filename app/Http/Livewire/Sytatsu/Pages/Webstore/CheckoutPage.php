<?php

declare(strict_types=1);

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Services\CartService;
use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Services\CheckoutService;
use Livewire\Features\SupportRedirects\Redirector;

class CheckoutPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.checkout';
    protected string $layout = 'layouts.sytatsu-layout';
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
        CheckoutService $checkoutService
    ): void {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function mount(): null|Redirector
    {
        if ($this->payment_intent) {
            $payment = $this->checkoutService->authorizePaymentIntent(
                paymentType: $this->paymentType,
                paymentIntent: $this->payment_intent,
                paymentIntentClientSecret: $this->payment_intent_client_secret,
            );

            if ($payment->success || $this->redirect_status === 'succeeded') {
                $orderId = $payment->orderId
                    ?: $this->checkoutService->getOrderIdByPaymentIntent($this->payment_intent)
                    ?: $this->checkoutService->getFirstOrderForCart($this->cart)?->id;

                if (! $orderId) {
                    logger()->error('Order not found after successful payment, redirecting to success page anyway', [
                        'payment_intent' => $this->payment_intent,
                        'cart_id' => $this->cart?->id,
                    ]);
                }

                session()->flash('checkout_success', true);

                return redirect()->route('sytatsu.webstore.checkout.success', ['order_id' => $orderId]);
            }
        }

        if ($this->cart->lines->isEmpty()) {
            return redirect()->route('sytatsu.webstore.welcome');
        }

        $this->mapLines();

        return null;
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
