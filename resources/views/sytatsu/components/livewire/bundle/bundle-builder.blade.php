{{-- Pinned to the bottom of the viewport, not just the page flow — this
     is the only place a bundle can be reviewed/completed/edited, so it
     must always be on screen, not just "while scrolled to the right
     spot" (which `sticky` alone wouldn't guarantee here, since it's not
     the last element in a container that fills the viewport height). --}}
<div class="fixed bottom-0 inset-x-0 z-50 px-4 pb-4 pointer-events-none">
    <div class="max-w-[85rem] mx-auto pointer-events-auto rounded-2xl shadow-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 p-4">
        <x-ui.spinner-overlay wire:loading.flex wire:target="addToCart, removeItem" />

        @if($added)
            <div class="flex items-center justify-center py-2">
                <p class="text-sm avenir-bold uppercase tracking-widest text-primary">
                    ✓ {{ $addedWasEdit ? __('Bundle updated!') : __('Bundle added to cart!') }}
                </p>
            </div>

            @if(!empty($bundleErrors))
                <ul class="mt-1 text-xs text-amber-600 dark:text-amber-400 list-disc list-inside text-center">
                    @foreach($bundleErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        @else
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                {{-- Picked items --}}
                <div class="flex-1 min-w-0">
                    @if($this->selectedItems->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Pick products below to start your bundle.') }}
                        </p>
                    @else
                        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                            @foreach($this->selectedItems as $item)
                                <div class="group relative size-10 rounded-md flex-shrink-0 border border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-900"
                                     title="{{ $item['name'] }}"
                                     wire:key="bundle-selected-{{ $item['variant_id'] }}"
                                >
                                    <img src="{{ $item['thumbnail'] ?? \App\Services\WebstoreHelperService::productPlaceholderImage() }}"
                                         alt="{{ $item['name'] }}" class="w-full h-full object-cover rounded-md">

                                    <span class="absolute -top-1.5 -right-1.5 bg-primary text-white text-[9px] font-bold size-4 rounded-full flex items-center justify-center avenir-bold leading-none">
                                        {{ $item['quantity'] }}
                                    </span>

                                    <button type="button"
                                            wire:click="removeItem({{ $item['variant_id'] }})"
                                            class="absolute inset-0 bg-black/40 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-md"
                                    >
                                        <i class="fa fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                        @if($this->currentTier)
                            <span class="avenir-bold">{{ $this->totalQuantity }}</span>
                            {{ __('picked') }} &middot;
                            {{ __(':price each', ['price' => $this->formatPrice($this->currentTier->price->value)]) }}
                        @else
                            <span class="avenir-bold">{{ $this->totalQuantity }}</span> {{ __('picked') }}
                        @endif

                        @if($this->nextTier)
                            &middot; {{ __('pick :more more to unlock :price each', [
                                'more' => $this->nextTier->min_quantity - $this->totalQuantity,
                                'price' => $this->formatPrice($this->nextTier->price->value),
                            ]) }}
                        @endif
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex items-center gap-2 shrink-0">
                    @if($editingLineId)
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Editing bundle in cart') }}</span>
                    @endif

                    <button type="button"
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary hover:opacity-90 transition-opacity disabled:opacity-50"
                    >
                        {{ $editingLineId ? __('Save changes') : __('Add bundle to cart') }}
                    </button>
                </div>
            </div>

            @if(!empty($bundleErrors))
                <ul class="mt-2 text-xs text-red-600 dark:text-red-400 list-disc list-inside">
                    @foreach($bundleErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>
</div>
