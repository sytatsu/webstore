<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Enums\CheckoutStepEnum;
use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\CartAddress;

/**
 * @property \Lunar\Models\Cart $cart
 */
class CheckoutForm extends Component
{

    private CartService $cartService;

    public ?CartAddress $shippingAddress = null;
    public ?CartAddress $billingAddress = null;

    public bool $isShippingSameAsBilling = true;

    public ?string $chosenShipping = null;

    public string $currentStep = CheckoutStepEnum::ADDRESS->value;

    protected $listeners = [
        'address-updated' => 'refreshAddresses',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->fillAddresses();
        $this->fillChosenShipping();
        $this->findCheckoutStep();
    }

    public function rules(): array
    {
        return $this->getAddressValidation('shipping');
    }

    public function getCartProperty(): \Lunar\Models\Cart
    {
        return $this->cartService->getCurrentCart();
    }

    public function toggleIsShippingSameAsBilling(): bool
    {
        return $this->isShippingSameAsBilling = !$this->isShippingSameAsBilling;
    }

    public function saveAddresses(): void
    {
        $this->dispatch('save-address');
    }

    public function refreshAddresses(): void
    {
        $this->cart->refresh();
        $this->shippingAddress = $this->cart->shippingAddress;

        if ($this->isShippingSameAsBilling) {
            $this->cart->setBillingAddress($this->shippingAddress);
            $this->billingAddress = $this->cart->refresh()->billingAddress;
        }

        if (!$this->isShippingSameAsBilling) {
            $this->billingAddress = $this->cart->billingAddress;
        }

        if ($this->shippingAddress && ($this->isShippingSameAsBilling || $this->billingAddress)) {
            $this->setCheckoutStep(CheckoutStepEnum::SHIPPING_OPTION->value);
        }
    }

    public function getShippingOptionsProperty(): Collection
    {
        return ShippingManifest::getOptions(
            $this->cart
        );
    }

    public function setChosenShipping(string $shipping): void
    {
        $this->chosenShipping = $shipping;
    }

    public function saveShippingOption(): void
    {
        $option = $this->shippingOptions->first(fn ($option) => $option->getIdentifier() == $this->chosenShipping);

        $this->cart->setShippingOption($option);
        $this->cart->refresh();

        $this->dispatch('cart-updated');
        $this->setCheckoutStep(CheckoutStepEnum::PAYMENT->value);;
    }

    private function findCheckoutStep(): void
    {
        if ($this->cart->shippingAddress === null) {
            $this->setCheckoutStep(CheckoutStepEnum::ADDRESS->value);
            return;
        }

        if ($this->cart->getShippingOption() === null) {
            $this->setCheckoutStep(CheckoutStepEnum::SHIPPING_OPTION->value);
            return;
        }

        $this->setCheckoutStep(CheckoutStepEnum::PAYMENT->value);
    }

    public function setCheckoutStep(string $step): void
    {
        if (CheckoutStepEnum::tryFrom($step)) {
            $this->currentStep = $step;
        }
    }

    private function fillChosenShipping(): void
    {
        $this->chosenShipping = $this->cart->getShippingOption()?->getIdentifier();
    }

    private function fillAddresses(): void
    {
        $this->cart->refresh();

        if ($this->cart->shippingAddress) {
            $this->shippingAddress = $this->cart->shippingAddress;
            $this->billingAddress  = $this->cart->billingAddress;
        }
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.checkout-form');
    }
}
