<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full px-4 md:px-6 lg:px-8 py-12 lg:py-24 flex flex-col-reverse md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-12 divide-y md:divide-y-0 md:divide-x divide-slate-800 dark:divide-slate-200">
    @if($this->cart && $this->lines)
        <div class="col-span-1 lg:col-span-2">
            <livewire:sytatsu.components.checkout-form/>
        </div>


        <div class="col-span-1">
            <livewire:sytatsu.components.cart.cart-details :checkout="true"/>
        </div>
    @else
        <div class="flex flex-col gap-4">
            <h1 class="text-slate-800 dark:text-white font-bold text-xl">{{ __('Whoops!') }}</h1>
            <span class="text-slate-800 dark:text-white">
                {{ __('Your cart appears to be empty, please add some items to your cart before checking out.') }}
            </span>
            <a class="block mr-auto py-3 px-5 text-sm font-medium text-center text-white bg-primary-dark rounded-lg hover:bg-primary" href="{{ route('sytatsu.webstore.welcome') }}">
                {{ __("Continue shopping") }}
            </a>
        </div>
    @endif
</div>
