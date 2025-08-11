

<div class="flex flex-col space-y-6">
    <div class="flex flex-col items-end">
        <p class="mb-2 text-2xl font-bold text-black dark:text-white">
            {{ $this->purchasable->basePrices->first()->price->formatted() }}
        </p>

        <span class="text-sm text-gray-400">({{ __('Inclusief belastingen') }})</span>

        @if($this->purchasable->purchasable === 'in_stock')
            @if ($this->availableStock !== 0)
                <span class="block text-primary">{{ $this->availableStock }} {{ __('Available') }}</span>
            @endif
        @endif
    </div>

    @if($this->purchasable->purchasable === 'in_stock' && $this->availableStock <= 0)
        <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center text-white bg-secondary rounded-lg" disabled>
            {{ __('Sold out') }}
        </button>
    @else
        <div class="flex flex-col sm:flex-row gap-4">
            <label for="quantity" class="sr-only">{{ __('Quantity') }}</label>
            <div class="flex rounded-lg border border-gray-100 dark:border-slate-800">
                <button type="button" class="size-11.5 m-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-s-md border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                        wire:click.prevent="increment()" @disabled($this->purchasable->purchasable === 'in_stock' && $this->availableStock <= $quantity)>
                    <i class="fa fa-plus"></i>
                </button>

                <input class="flex-grow sm:w-12 px-1 py-2 text-sm text-center transition-colors border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 [&::-webkit-inner-spin-button]:appearance-none focus:outline-none"
                       type="number"
                       id="quantity"
                       min="1"
                       value="1"
                       wire:model.blur="quantity" />

                <button type="button" class="size-11.5 m-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-e-md border border-transparent text-black dark:text-white bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                        wire:click.prevent="decrement()" @disabled($quantity <= 1)>
                    <i class="fa fa-minus"></i>
                </button>
            </div>

            <button class="w-full sm:w-auto size-11.5 sm:flex-grow px-6 m-0 text-sm font-medium text-center text-white bg-primary-dark rounded-lg hover:bg-primary disabled:opacity-50"
                    type="submit" wire:click.prevent="addToCart()" wire:loading.attr="disabled">
                {{ __('Add to shopping cart') }}
            </button>
        </div>

        @if ($errors->has('quantity'))
            <div class="p-2 mt-4 text-xs font-medium text-center text-red-700 rounded bg-red-50" role="alert">
                @foreach ($errors->get('quantity') as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
   @endif
</div>

