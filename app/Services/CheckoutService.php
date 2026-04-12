<?php

namespace App\Services;

use App\Repositories\CartAddressRepository;
use App\Repositories\CountryRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Collection;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use Lunar\Stripe\Models\StripePaymentIntent;

readonly class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private CartAddressRepository $cartAddressRepository,
        private CountryRepository $countryRepository,
        private OrderRepository $orderRepository
    ) {
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

    public function getOrderIdByPaymentIntent(string $paymentIntent): ?int
    {
        return StripePaymentIntent::where('intent_id', $paymentIntent)->first()?->order_id;
    }

    public function updateCartAddress(int $id, array $data): bool
    {
        return $this->cartAddressRepository->update($id, $data);
    }

    public function setShippingAddress(Cart $cart, array $address): array
    {
        $cart->setShippingAddress($address);
        $cart->save();
        return $cart->refresh()->shippingAddress->toArray();
    }

    public function setBillingAddress(Cart $cart, array $address): array
    {
        $cart->setBillingAddress($address);
        $cart->save();
        return $cart->refresh()->billingAddress->toArray();
    }

    public function syncAddresses(Cart $cart, bool $isShippingSameAsBilling): void
    {
        $cart->refresh();
        if ($isShippingSameAsBilling && $cart->shippingAddress) {
            $cart->setBillingAddress($cart->shippingAddress);
        }
    }

    public function getAddressValidationRules(string $prefix = 'address'): array
    {
        return [
            "{$prefix}.first_name"    => 'required',
            "{$prefix}.last_name"     => 'required',
            "{$prefix}.line_one"      => 'required',
            "{$prefix}.line_two"      => 'required',
            "{$prefix}.line_three"    => 'nullable',
            "{$prefix}.postcode"      => 'required',
            "{$prefix}.city"          => 'required',
            "{$prefix}.country_id"    => 'required',
            "{$prefix}.contact_email" => 'required|email',
            "{$prefix}.contact_phone" => 'nullable',
        ];
    }

    public function getAvailableCountries(): Collection
    {
        return $this->countryRepository->getAvailableCountries();
    }

    public function findOrder(int|string $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    public function getFirstOrderForCart(Cart $cart): ?Order
    {
        return $this->orderRepository->findFirstByCart($cart);
    }
}
