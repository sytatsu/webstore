<div class="flex flex-col w-full px-4 gap-12">
    <script src="https://js.stripe.com/basil/stripe.js"></script>

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::ADDRESS->value)

        <div class="flex flex-col w-full gap-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white col-span-2">{{ __('Shipping Address') }}</h1>
            <livewire:sytatsu.components.checkout.address-form address-type="{{ \App\Enums\AddressTypeEnum::SHIPPING->value }}" :address="$this->shippingAddress?->toArray() ?? []"/>

            @if($this->isShippingSameAsBilling)
                <div class="grid grid-cols-2 gap-4">
                    <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center rounded-lg text-slate-800 dark:text-white hover:underline focus:outline-hidden focus:underline transition disabled:opacity-50 disabled:pointer-events-none"
                            type="submit" wire:click.prevent="toggleIsShippingSameAsBilling()" wire:loading.attr="disabled">
                        {{ __('Add billing Address') }}
                    </button>

                    <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center text-white bg-primary-dark rounded-lg hover:bg-primary disabled:opacity-50"
                            type="submit" wire:click.prevent="saveAddresses()" wire:loading.attr="disabled">
                        {{ __('Continue') }}
                    </button>
                </div>
            @endif
        </div>

        @if(!$this->isShippingSameAsBilling)
            <hr class="text-slate-800 dark:text-white"/>

            <div class="flex flex-col w-full gap-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white col-span-2">{{ __('Billing Address') }}</h1>
                <livewire:sytatsu.components.checkout.address-form address-type="{{ \App\Enums\AddressTypeEnum::BILLING->value }}" :address="$this->billingAddress?->toArray() ?? []"/>

                <div class="grid grid-cols-2 gap-4">
                    <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center rounded-lg text-slate-800 dark:text-white hover:underline focus:outline-hidden focus:underline transition disabled:opacity-50 disabled:pointer-events-none"
                            type="submit" wire:click.prevent="toggleIsShippingSameAsBilling()" wire:loading.attr="disabled">
                        {{ __('Remove billing address') }}
                    </button>

                    <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center text-white bg-primary-dark rounded-lg hover:bg-primary disabled:opacity-50"
                            type="submit" wire:click.prevent="saveAddresses()" wire:loading.attr="disabled">
                        {{ __('Continue') }}
                    </button>
                </div>
            </div>
        @endif
    @endif

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value)

        <div class="flex flex-col w-full gap-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($this->shippingOptions as $option)
                    <div>
                        <input class="hidden peer" type="radio" wire:model.blur="chosenShipping" wire:change="setChosenShipping('{{$option->getIdentifier()}}')"
                               name="shippingOption" value="{{ $option->getIdentifier() }}" id="{{ $option->getIdentifier() }}"
                               @selected($this->chosenShipping === $option->getIdentifier())
                        />

                        <label class="flex flex-col gap-2 items-center justify-between px-6 py-4 text-sm font-medium text-center rounded-lg border border-primary-dark text-slate-800 dark:text-white hover:bg-primary focus:outline-hidden focus:bg-primary-dark transition disabled:opacity-50 disabled:pointer-events-none peer-checked:border-primary hover:bg-primary peer-checked:ring-1 peer-checked:bg-primary dark:peer-checked:bg-primary-dark peer-checked:ring-primary cursor-pointer peer-checked:cursor-default"
                               for="{{ $option->getIdentifier() }}">
                            <span class="avenir-bold">{{ __($option->getName()) }} - {{ $option->getPrice()->formatted() }}</span>
                            <span>{{ $option->getDescription() }}</span>
                        </label>
                    </div>
                @endforeach
            </div>

            @if ($errors->has('chosenShipping'))
                <p class="p-4 text-sm text-red-500">
                    {{ $errors->first('chosenShipping') }}
                </p>
            @endif

            <div class="grid grid-cols-1 gap-4">
                <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center text-white bg-primary-dark rounded-lg hover:bg-primary disabled:opacity-50"
                        type="submit" wire:click.prevent="saveShippingOption()" wire:loading.attr="disabled" @disabled(!$this->chosenShipping)>
                    {{ __('Continue') }}
                </button>
                <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center rounded-lg text-slate-800 dark:text-white hover:underline focus:outline-hidden focus:underline transition disabled:opacity-50 disabled:pointer-events-none"
                        type="submit" wire:click.prevent="setCheckoutStep('{{ \App\Enums\CheckoutStepEnum::ADDRESS->value }}')" wire:loading.attr="disabled">
                    {{ __('Return to address') }}
                </button>
            </div>
        </div>
    @endif

    @if ($this->currentStep === \App\Enums\CheckoutStepEnum::PAYMENT->value)

        <div class="flex flex-col w-full gap-4">
            <livewire:sytatsu.components.checkout.payment-form/>

            <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center rounded-lg text-slate-800 dark:text-white hover:underline focus:outline-hidden focus:underline transition disabled:opacity-50 disabled:pointer-events-none"
                    type="submit" wire:click.prevent="setCheckoutStep('{{ \App\Enums\CheckoutStepEnum::SHIPPING_OPTION->value }}')" wire:loading.attr="disabled">
                {{ __('Return to shipping') }}
            </button>
        </div>
    @endif
</div>
