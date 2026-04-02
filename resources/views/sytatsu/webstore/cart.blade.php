<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 py-12 flex flex-col gap-8">
    @if ($this->cart && count($this->cart->lines))
        <div class="flex flex-col md:grid md:grid-cols-3 gap-8 md:gap-12">
            <div class="md:col-span-2 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 self-start w-full">
                <h2 class="text-2xl font-bold text-black dark:text-white mb-8 avenir-bold uppercase">
                    {{ __('Shopping Cart') }}
                </h2>

                <hr class="mb-8 border-gray-200 dark:border-gray-500">

                <livewire:sytatsu.components.cart.components.cart-items />
            </div>

            <div class="md:col-span-1 flex flex-col gap-8">
                <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-6 avenir-bold uppercase">
                        {{ __('Order Summary') }}
                    </h3>

                    <hr class="mb-6 border-gray-200 dark:border-gray-500">

                    <livewire:sytatsu.components.cart.components.cart-totals />

                    <div class="mt-8">
                        <x-ui.button.default.primary class="w-full text-center" href="{{ route('sytatsu.webstore.checkout') }}">
                            {{ __('Proceed to Checkout') }}
                        </x-ui.button.default.primary>
                    </div>
                </div>

                <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12">
                    <h3 class="text-lg font-bold text-black dark:text-white mb-4 avenir-bold uppercase text-sm">
                        {{ __('Need Help?') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('If you have any questions about your order, please contact our support.') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-12 text-center">
            <h2 class="text-2xl font-bold text-black dark:text-white mb-6 avenir-bold uppercase">
                {{ __('Your Cart is Empty') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                {{ __('Looks like you haven\'t added anything to your cart yet.') }}
            </p>
            <x-ui.button.default.primary href="{{ route('sytatsu.webstore.welcome') }}">
                {{ __('Go Shopping') }}
            </x-ui.button.default.primary>
        </div>
    @endif
</div>
