<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Enums\CheckoutStepEnum;
use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;

/**
 * @property \Lunar\Models\Cart $cart
 */
class CheckoutForm extends Component
{
    private CartService $cartService;

    private CartAddress $shippingAddress;
    private CartAddress $billingAddress;

    public bool $isShippingSameAsBilling = true;
    public string $currentStep = CheckoutStepEnum::ADDRESS->value;

    protected $listeners = [
        'address-updated' => 'refreshAddress',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function rules(): array
    {
        return $this->getAddressValidation('shipping');
    }

    public function getCartProperty(): Cart
    {
        return $this->cartService->getCurrentCart();
    }

    public function getCountriesProperty(): Collection
    {
        return Country::whereIn('iso3', ['NLD'])->get();
    }

    public function toggleIsShippingSameAsBilling(): bool
    {
        return $this->isShippingSameAsBilling = !$this->isShippingSameAsBilling;
    }

    public function refreshAddresses(): void
    {
        $this->cart->refresh();
        $this->shippingAddress = $this->cart->shippingAddress;

        if ($this->isShippingSameAsBilling) {
            $this->cart->setBillingAddress($this->shippingAddress);
            $this->shippingAddress = $this->cart->billingAddress;
        }

        if (!$this->isShippingSameAsBilling) {
            $this->billingAddress = $this->cart->billingAddress;
        }
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.checkout-form');
    }
}
