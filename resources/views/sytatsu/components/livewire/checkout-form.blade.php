<div class="flex flex-col w-full gap-12">
    {{-- Checkout Wizard --}}
    <div class="w-full py-4 border-b border-gray-200 dark:border-slate-700">
        <div class="flex items-center justify-between max-w-2xl mx-auto">
            {{-- Step 1: Address --}}
            <div class="flex flex-col items-center gap-2 group cursor-pointer" wire:click="setCheckoutStep('{{ \App\Enums\CheckoutStepEnum::ADDRESS->value }}')">
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 {{ $this->currentStep === \App\Enums\CheckoutStepEnum::ADDRESS->value ? 'bg-primary text-white ring-4 ring-primary/20' : ($this->shippingAddress ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400') }}">
                    @if($this->currentStep !== \App\Enums\CheckoutStepEnum::ADDRESS->value && $this->shippingAddress)
                        <i class="fa fa-check"></i>
                    @else
                        <i class="fa fa-map-marker-alt"></i>
                    @endif
                </div>
                <span class="text-xs font-bold avenir-bold uppercase {{ $this->currentStep === \App\Enums\CheckoutStepEnum::ADDRESS->value ? 'text-primary' : 'text-gray-500' }}">
                    {{ __('Address') }}
                </span>
            </div>

            <div class="flex-grow h-0.5 {{ $this->shippingAddress ? 'bg-green-500' : 'bg-gray-200 dark:bg-slate-700' }} mx-4 -mt-6"></div>

            {{-- Step 2: Shipping --}}
            <div class="flex flex-col items-center gap-2 group {{ $this->shippingAddress ? 'cursor-pointer' : 'opacity-50 pointer-events-none' }}" @if($this->shippingAddress) wire:click="setCheckoutStep('{{ \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value }}')" @endif>
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 {{ $this->currentStep === \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value ? 'bg-primary text-white ring-4 ring-primary/20' : ($this->chosenShipping ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400') }}">
                    @if($this->currentStep !== \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value && $this->chosenShipping)
                        <i class="fa fa-check"></i>
                    @else
                        <i class="fa fa-truck"></i>
                    @endif
                </div>
                <span class="text-xs font-bold avenir-bold uppercase {{ $this->currentStep === \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value ? 'text-primary' : 'text-gray-500' }}">
                    {{ __('Shipping') }}
                </span>
            </div>

            <div class="flex-grow h-0.5 {{ $this->chosenShipping ? 'bg-green-500' : 'bg-gray-200 dark:bg-slate-700' }} mx-4 -mt-6"></div>

            {{-- Step 3: Payment --}}
            <div class="flex flex-col items-center gap-2 group {{ $this->chosenShipping ? 'cursor-pointer' : 'opacity-50 pointer-events-none' }}" @if($this->chosenShipping) wire:click="setCheckoutStep('{{ \App\Enums\CheckoutStepEnum::PAYMENT->value }}')" @endif>
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 {{ $this->currentStep === \App\Enums\CheckoutStepEnum::PAYMENT->value ? 'bg-primary text-white ring-4 ring-primary/20' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400' }}">
                    <i class="fa fa-credit-card"></i>
                </div>
                <span class="text-xs font-bold avenir-bold uppercase {{ $this->currentStep === \App\Enums\CheckoutStepEnum::PAYMENT->value ? 'text-primary' : 'text-gray-500' }}">
                    {{ __('Payment') }}
                </span>
            </div>
        </div>
    </div>

    <div
        wire:loading.flex
        x-show="$wire.isProcessing"
        wire:target="saveAddresses, toggleIsShippingSameAsBilling, refreshAddresses, saveShippingOption, setCheckoutStep"
        style="display: none;"
        class="absolute inset-0 z-50 flex items-center justify-center bg-white/50 dark:bg-slate-800/50"
    >
        <svg class="w-12 h-12 text-primary animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <script src="https://js.stripe.com/basil/stripe.js"></script>

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::ADDRESS->value)
        <div class="flex flex-col w-full gap-12 ">
            <div class="flex flex-col w-full gap-8">
                <div class="border-b border-gray-200 dark:border-slate-700 pb-2">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white avenir-bold uppercase flex items-center gap-2">
                        <i class="fa fa-truck-fast text-primary"></i>
                        {{ __('Shipping Address') }}
                    </h1>
                </div>
                <div class="p-2">
                    <livewire:sytatsu.components.checkout.address-form address-type="{{ \App\Enums\AddressTypeEnum::SHIPPING->value }}" :address="$this->shippingAddress?->toArray() ?? []"/>
                </div>

                @if($this->isShippingSameAsBilling)
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <x-ui.button.default.primary type="button" class="w-full sm:w-auto px-12" wire:click.prevent="saveAddresses()" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveAddresses">{{ __('Continue') }}</span>
                            <div wire:loading wire:target="saveAddresses" class="flex items-center justify-center">
                                {{ __('Processing') }}
                            </div>
                        </x-ui.button.default.primary>

                        <x-ui.button.link.default type="button" class="w-full sm:w-auto" wire:click.prevent="toggleIsShippingSameAsBilling()" wire:loading.attr="disabled">
                            {{ __('Add billing Address') }}
                        </x-ui.button.link.default>
                    </div>
                @endif
            </div>

            @if(!$this->isShippingSameAsBilling)
                <div class="flex flex-col w-full gap-8">
                    <div class="border-b border-gray-200 dark:border-slate-700 pb-2">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white avenir-bold uppercase flex items-center gap-2">
                            <i class="fa fa-file-invoice-dollar text-primary"></i>
                            {{ __('Billing Address') }}
                        </h1>
                    </div>
                    <div class="p-2">
                        <livewire:sytatsu.components.checkout.address-form address-type="{{ \App\Enums\AddressTypeEnum::BILLING->value }}" :address="$this->billingAddress?->toArray() ?? []"/>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <x-ui.button.default.primary class="w-full sm:w-auto px-12" type="button" wire:click.prevent="saveAddresses()" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveAddresses">{{ __('Continue') }}</span>
                            <div wire:loading wire:target="saveAddresses" class="flex items-center justify-center">
                                {{ __('Processing') }}
                            </div>
                        </x-ui.button.default.primary>

                        <x-ui.button.link.default class="w-full sm:w-auto" type="button" wire:click.prevent="toggleIsShippingSameAsBilling()" wire:loading.attr="disabled">
                            {{ __('Remove billing address') }}
                        </x-ui.button.link.default>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value)
        <div class="flex flex-col w-full gap-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($this->shippingOptions as $option)
                    <div
                        wire:key="shipping_option_{{ $option->getIdentifier() }}"
                        wire:click="$set('chosenShipping', '{{ $option->getIdentifier() }}')"
                        class="relative flex flex-col h-full p-6 text-sm font-medium rounded-lg border-2 cursor-pointer transition-all {{ $this->chosenShipping === $option->getIdentifier() ? 'border-secondary ring-1 ring-secondary bg-secondary/5 dark:bg-secondary/10' : 'border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-primary/50' }} text-slate-800 dark:text-white"
                    >
                        <div class="flex justify-between items-start mb-2">
                            <span class="avenir-bold text-lg">{{ __($option->getName()) }}</span>
                            <span class="text-secondary avenir-bold">{{ $option->getPrice()->formatted() }}</span>
                        </div>
                        @if($option->getDescription())
                            <span class="text-gray-500 dark:text-gray-400 font-normal">{{ $option->getDescription() }}</span>
                        @endif
                        <div class="mt-auto pt-4 flex justify-end">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $this->chosenShipping === $option->getIdentifier() ? 'border-secondary' : 'border-gray-300 dark:border-slate-600' }}">
                                @if($this->chosenShipping === $option->getIdentifier())
                                    <div class="w-2.5 h-2.5 rounded-full bg-secondary"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($errors->has('chosenShipping'))
                <p class="p-4 text-sm text-red-500 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    {{ $errors->first('chosenShipping') }}
                </p>
            @endif

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                @if(!$this->chosenShipping)
                    <x-ui.button.default.primary class="w-full sm:w-auto px-12"
                            type="button" disabled>
                        {{ __('Continue') }}
                    </x-ui.button.default.primary>
                @else
                    <x-ui.button.default.primary class="w-full sm:w-auto px-12"
                            type="button" wire:click.prevent="saveShippingOption()" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveShippingOption">{{ __('Continue') }}</span>
                        <div wire:loading wire:target="saveShippingOption" class="flex items-center justify-center">
                            {{ __('Processing') }}
                        </div>
                    </x-ui.button.default.primary>
                @endif

                <x-ui.button.link.default class="w-full sm:w-auto"
                        type="button" wire:click.prevent="setCheckoutStep('{{ \App\Enums\CheckoutStepEnum::ADDRESS->value }}')" wire:loading.attr="disabled">
                    {{ __('Return to address') }}
                </x-ui.button.link.default>
            </div>
        </div>
    @endif

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::PAYMENT->value)
        <div class="flex flex-col w-full gap-8">
            <livewire:sytatsu.components.checkout.payment-form/>
        </div>
    @endif
</div>
