<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Enums\CheckoutStepEnum;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\CartAddress;
use Lunar\Stripe\Facades\Stripe as StripeFacade;

/**
 * @property \Lunar\Models\Cart $cart
 */
class CheckoutForm extends Component
{

    private CartService $cartService;
    private CheckoutService $checkoutService;

    public ?CartAddress $shippingAddress = null;
    public ?CartAddress $billingAddress = null;

    public bool $isShippingSameAsBilling = true;

    public ?string $chosenShipping = null;

    public string $currentStep = CheckoutStepEnum::ADDRESS->value;

    protected $listeners = [
        'address-updated' => 'refreshAddresses',
        'address-save-failed' => 'stopProcessing',
        'cart-updated' => 'onCartUpdated',
    ];

    public function boot(
        CartService $cartService,
        CheckoutService $checkoutService
    ): void {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function mount()
    {
        $this->fillAddresses();
        $this->fillChosenShipping();
        $this->findCheckoutStep();
    }

    public function rules(): array
    {
        return $this->checkoutService->getAddressValidationRules('shipping');
    }

    public function getCartProperty(): \Lunar\Models\Cart
    {
        return $this->cartService->getCurrentCart();
    }

    public function toggleIsShippingSameAsBilling(): bool
    {
        return $this->isShippingSameAsBilling = !$this->isShippingSameAsBilling;
    }

    public bool $isProcessing = false;

    public function saveAddresses(): void
    {
        $this->isProcessing = true;
        $this->dispatch('save-address');
    }

    public function refreshAddresses(): void
    {
        $this->isProcessing = true;
        $this->checkoutService->syncAddresses($this->cart, $this->isShippingSameAsBilling);
        $this->fillAddresses();

        $this->onCartUpdated();

        if ($this->shippingAddress && ($this->isShippingSameAsBilling || $this->billingAddress)) {
            $this->setCheckoutStep(CheckoutStepEnum::SHIPPING_OPTION->value);
        }

        $this->dispatch('cart-updated');
    }

    public function onCartUpdated(): void
    {
        $this->cart->refresh();

        if ($this->currentStep === CheckoutStepEnum::PAYMENT->value) {
            StripeFacade::syncIntent($this->cart);
        }

        $option = $this->cartService->recalculateShippingOption($this->cart);
        $this->cart->setShippingOption($option);
        $this->fillChosenShipping();
    }

    public function updatedChosenShipping($value): void
    {
        $option = $this->shippingOptions->first(fn ($option) => $option->getIdentifier() == $value);

        if ($option) {
            $this->cart->setShippingOption($option);
            $this->cart->refresh();

            $this->dispatch('cart-updated');
            $this->dispatch('shipping-option-updated');
        }
    }

    public function getShippingOptionsProperty(): Collection
    {
        return $this->cartService->getAvailableShippingOptions($this->cart);
    }

    public function saveShippingOption(): void
    {
        $this->isProcessing = true;
        $this->setCheckoutStep(CheckoutStepEnum::PAYMENT->value);
        $this->onCartUpdated();
        $this->isProcessing = false;
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

    public function stopProcessing(): void
    {
        $this->isProcessing = false;
    }

    public function setCheckoutStep(string $step): void
    {
        if (CheckoutStepEnum::tryFrom($step)) {
            $this->currentStep = $step;
            $this->isProcessing = false;
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
