<div class="sm:relative"
     x-data="{
         cartOpen: @entangle('cartOpen')
     }">

    <button class="relative grid w-16 h-16 transition hover:opacity-75  m-auto"
            x-on:click="cartOpen = !cartOpen">
        <span class="sr-only">
            {{ __('Cart') }}
        </span>
        <div class="place-self-center">
            <i class="fa fa-cart-shopping text-size-lg text-slate-800 dark:text-white"></i>
            @if ($this->cartTotalQuantity > 0)
                <div class="absolute py-0.5 px-1 top-2 right-2 text-size-sm bg-secondary rounded-full aspect-square text-white">
                    {{ $this->cartTotalQuantity }}
                </div>
            @endif
        </div>
    </button>

    <div class="absolute inset-x-0 top-auto z-50 w-screen max-w-sm px-6 py-8 mx-auto mt-8 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-lg dark:shadow-primary/30 sm:left-auto rounded-xl"
         x-show="cartOpen"
         x-on:click.away="cartOpen = false"
         x-transition
         x-cloak>
        <button class="absolute text-gray-500 dark:text-gray-100 transition-transform top-3 right-3 hover:scale-110"
                type="button"
                aria-label="Close"
                x-on:click="cartOpen = false">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <livewire:sytatsu.components.cart.cart-details />
    </div>
</div>
