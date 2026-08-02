<div class="flex flex-col space-y-6 w-full relative">
    @if (!$this->minimalistic)
        <div class="flex flex-col items-end">
            @if ($this->purchasable && $this->purchasable->basePrices->first())
                <p class="mb-1 text-2xl font-bold text-black dark:text-white avenir-bold uppercase">
                    {{ $this->purchasable->basePrices->first()->price->formatted() }}
                </p>

                <span class="font-mono text-[10px] tracking-[.16em] uppercase text-gray-400">({{ __('Including Taxes') }})</span>
            @endif

            @if($this->purchasable && $this->purchasable->purchasable === 'in_stock')
                @if ($this->availableStock !== 0)
                    <span class="block mt-2 font-mono text-[10px] tracking-[.16em] uppercase text-primary">{{ $this->availableStock }} {{ __('Available') }}</span>
                @endif
            @endif
        </div>
    @endif

    @if($this->purchasable && $this->purchasable->purchasable === 'in_stock' && $this->availableStock <= 0)
        <x-ui.button.default.secondary class="w-full" disabled>
            {{ __('Sold out') }}
        </x-ui.button.default.secondary>
    @else
        <div class="flex flex-col sm:flex-row gap-4">
            @if (!$this->minimalistic)
                <label for="quantity" class="sr-only">{{ __('Quantity') }}</label>
                <div class="flex rounded-xl overflow-hidden bg-gray-50 dark:bg-slate-900">
                    <button type="button" class="size-11.5 m-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold border border-transparent text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                            wire:loading.attr="disabled" wire:click.prevent="increment()" @disabled($this->purchasable && $this->purchasable->purchasable === 'in_stock' && $this->availableStock <= $quantity)>
                        <i class="fa fa-plus"></i>
                    </button>

                    <input class="flex-grow sm:w-12 px-1 py-2 text-sm text-center transition-colors text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 [&::-webkit-inner-spin-button]:appearance-none focus:outline-none disabled:pointer-events-none"
                           type="number"
                           id="quantity"
                           min="1"
                           value="1"
                           wire:model.blur="quantity"
                           wire:loading.attr="disabled"/>

                    <button type="button" class="size-11.5 m-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold border border-transparent text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                            wire:loading.attr="disabled" wire:click.prevent="decrement()" @disabled($quantity <= 1)>
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            @endif

            <x-ui.button.default.primary class="w-full" type="submit" wire:click.prevent="addToCart()" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="addToCart">{{ __('Add to shopping cart') }}</span>
                <div wire:loading wire:target="addToCart" class="flex items-center justify-center flex-nowrap">
                    <x-ui.loader />
                    <span>{{ __('Processing') }}</span>
                </div>
            </x-ui.button.default.primary>
        </div>

        <x-ui.field-error field="quantity" />
   @endif
</div>

