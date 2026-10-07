<div class="flex flex-col space-y-6 w-full relative">
    @if($this->activeBundle)
        {{-- This product is locked into a bundle — the bundle builder is the
             only way to acquire it, so the normal add-to-cart flow below is
             replaced entirely, not just supplemented. --}}
        @if (!$this->minimalistic)
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ __('Only available as part of the :name.', ['name' => $this->activeBundle->getTranslatedName()]) }}
            </p>
        @endif

        <div class="flex items-center gap-2">
            {{-- `x-ref="bundleAddTrigger"` on both of this button's states (here,
                 and the stepper's own "+" below) — product-tile.blade.php makes
                 the *whole* tile clickable to add one, by forwarding a click to
                 whichever of these two is actually showing, rather than
                 duplicating the addToBundle() call outside this component. --}}
            @if ($this->bundleQuantity <= 0 && $this->bundleAvailable <= 0)
                {{-- Same "Sold out" state the non-bundle flow below already
                     has for `availableStock <= 0` — this branch was missing
                     here entirely, so a bundle-locked product with no stock
                     left just showed a perfectly normal, clickable "Add to
                     bundle" button (addToBundle() itself already no-ops
                     server-side via the `bundleQuantity >= bundleAvailable`
                     guard, but nothing in the UI ever said why). --}}
                <x-ui.button.default.secondary class="w-full" disabled>
                    {{ __('Sold out') }}
                </x-ui.button.default.secondary>
            @elseif ($this->bundleQuantity <= 0)
                {{-- A plain `wire:loading.attr="disabled"` (same as the
                     standalone add-to-cart button below) stops a second
                     *request* from doing anything once the button is
                     actually disabled, but gives no feedback in the gap
                     before that — rapid clicks there (or the whole-tile
                     click forwarding in product-tile.blade.php hammering
                     this same button) read as nothing happening, which is
                     exactly when someone clicks again. A visible spinner,
                     targeted so it only shows for *this* button's own
                     request, makes "it's working" obvious immediately. --}}
                <x-ui.button.default.primary class="w-full" type="button" x-ref="bundleAddTrigger" wire:click.prevent="addToBundle()" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="addToBundle">{{ __('Add to bundle') }}</span>
                    <div wire:loading wire:target="addToBundle" class="flex items-center justify-center flex-nowrap">
                        <x-ui.loader />
                        <span>{{ __('Adding') }}</span>
                    </div>
                </x-ui.button.default.primary>
            @else
                <div class="flex flex-1 rounded-xl overflow-hidden bg-gray-50 dark:bg-slate-900">
                    <button type="button" class="size-11.5 m-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold border border-transparent text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                            wire:loading.attr="disabled" wire:click.prevent="removeFromBundleOne()">
                        <i class="fa fa-minus" wire:loading.remove wire:target="removeFromBundleOne"></i>
                        <i class="fa fa-spinner fa-spin" wire:loading wire:target="removeFromBundleOne"></i>
                    </button>

                    <span class="flex-grow px-1 py-2 text-sm text-center text-black dark:text-white flex items-center justify-center">
                        {{ $this->bundleQuantity }}
                    </span>

                    {{-- Same spinner swap as the "Add to bundle" button above
                         — this "+" is the other state `bundleAddTrigger` can
                         be in, and the whole-tile click forwarding hits
                         whichever one is showing. --}}
                    <button type="button" class="size-11.5 m-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold border border-transparent text-black dark:text-white bg-transparent hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                            x-ref="bundleAddTrigger" wire:loading.attr="disabled" wire:click.prevent="addToBundle()" @disabled($this->bundleQuantity >= $this->bundleAvailable)>
                        <i class="fa fa-plus" wire:loading.remove wire:target="addToBundle"></i>
                        <i class="fa fa-spinner fa-spin" wire:loading wire:target="addToBundle"></i>
                    </button>
                </div>

                @if (!$this->minimalistic)
                    <button type="button" class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:underline whitespace-nowrap" wire:click.prevent="removeFromBundle()">
                        {{ __('Remove') }}
                    </button>
                @endif
            @endif
        </div>
    @else
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
    @endif
</div>
