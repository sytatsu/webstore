<?php

namespace App\Services;

use Lunar\Facades\Payments;
use Lunar\Models\Cart;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService
    ) {
        //
    }

    public function getCart(): Cart
    {
        return $this->cartService->getCurrentCart();
    }

    public function authorizePaymentIntent(
        string $paymentType,
        string $paymentIntent,
        string $paymentIntentClientSecret,
    ) {
        return Payments::driver($paymentType)->cart($this->getCart())->withData([
            'payment_intent_client_secret' => $paymentIntentClientSecret,
            'payment_intent' => $paymentIntent
        ])->authorize();
    }
}
