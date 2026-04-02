<div class="flex-grow">
    @if ($this->cart && $this->lines)
        <h3 class="text-xl font-bold text-black dark:text-white mb-6 avenir-bold uppercase">
            {{ $checkout ? __('Order Summary') : __('Shopping Cart') }}
        </h3>

        <hr class="mb-6 border-gray-200 dark:border-gray-500">

        <div class="flow-root mb-6">
            <livewire:sytatsu.components.cart.components.cart-items :checkout="$checkout" />
        </div>

        <hr class="mb-6 border-gray-200 dark:border-gray-500">

        <livewire:sytatsu.components.cart.components.cart-totals :checkout="$checkout" />
    @else
        <p class="py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-300">
            {{ __('Your cart is empty') }}
        </p>
    @endif

    @if (!$this->isCartDisabled())
        @if ($this->cart && $this->lines)
                @if (!$this->checkout)
                    <div class="mt-4 space-y-4 text-center">
                        <x-ui.button.default.primary class="w-full" href="{{ route('sytatsu.webstore.checkout') }}">
                            {{ __('Checkout') }}
                        </x-ui.button.default.primary>
                    </div>
                @endif

            <div class="mt-4 space-y-4 text-center">
                <x-ui.button.default.secondary class="w-full" href="{{ route('sytatsu.webstore.cart') }}">
                    {{ __('Overview') }}
                </x-ui.button.default.secondary>
            </div>
        @endif
   @endif
</div>
