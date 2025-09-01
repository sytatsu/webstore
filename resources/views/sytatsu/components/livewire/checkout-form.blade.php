<div class="w-full grid grid-cols-2 gap-4 px-4">
    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::ADDRESS->value)
        <wire:sytatsu.components.checkout.address-form :type="\App\Enums\AddressType::SHIPPING->value"/>
    @endif

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value)
        // Shipping
    @endif

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::PAYMENT->value)
        // Payment
    @endif
</div>
