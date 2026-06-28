<div
    x-data="{
        isStuck: false,
        init() {
            const sentinel = document.getElementById('bundle-panel-sentinel');
            if (!sentinel) return;
            const observer = new IntersectionObserver(
                ([entry]) => {
                    this.isStuck = !entry.isIntersecting;
                    sentinel.style.height = this.isStuck ? this.$el.offsetHeight + 'px' : '';
                },
                { threshold: 0, rootMargin: '0px' }
            );
            observer.observe(sentinel);
        }
    }"
    :class="isStuck ? 'fixed top-0 left-0 z-50 w-full shadow-lg' : 'relative'"
>
    <div class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-4 py-3"
         x-data="{ showSuccess: false }"
         x-on:bundle-added-success.window="
            showSuccess = true;
            setTimeout(() => { showSuccess = false }, 3000);
         "
    >
        <x-ui.spinner-overlay wire:loading.flex wire:target="addToCart, removeItem, clear" />

        <template x-if="showSuccess">
            <div class="max-w-[85rem] mx-auto flex items-center gap-3 animate-pulse">
                <p class="text-sm font-semibold text-primary avenir-bold uppercase tracking-widest">
                    ✓ {{ __('Bundle added to cart!') }}
                </p>
            </div>
        </template>

        <div x-show="!showSuccess" class="max-w-[85rem] mx-auto flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
            {{-- Bundle name label --}}
            @if ($this->bundleName)
                <span class="text-xs font-bold uppercase tracking-widest text-black dark:text-white flex-shrink-0 avenir-bold">
                    {{ $this->bundleName }}
                </span>
            @endif

            {{-- Thumbnail row --}}
            <div class="flex items-center gap-2 flex-1 min-w-0 overflow-x-auto no-scrollbar py-2">
                @forelse ($selectedItems as $variantId => $item)
                    <div
                        class="group relative w-10 h-10 rounded flex-shrink-0 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700"
                        title="{{ $item['name'] }}"
                    >
                        @if ($item['thumbnail'])
                            <img src="{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover rounded" />
                        @else
                            <div class="w-full h-full bg-gray-100 dark:bg-slate-700 rounded"></div>
                        @endif

                        {{-- Quantity Badge --}}
                        <div class="absolute -top-1.5 -right-1.5 bg-primary text-white text-[8px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white dark:border-slate-800 avenir-bold leading-none">
                            {{ $item['quantity'] }}
                        </div>

                        {{-- Delete Button --}}
                        <button
                            type="button"
                            wire:click="removeItem('{{ $variantId }}')"
                            class="absolute inset-0 bg-black/40 text-white opacity-0 group-hover:opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity flex items-center justify-center rounded z-10"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>

                        {{-- Mobile Delete Indicator (Visible on mobile to show it's interactive) --}}
                        <div class="sm:hidden absolute top-0.5 left-0.5 bg-black/20 rounded-full p-0.5 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2 w-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                    </div>
                @empty
                    <span class="text-sm text-gray-400 dark:text-gray-500 avenir-bold uppercase tracking-widest">{{ __('Select products to build your bundle') }}</span>
                @endforelse
            </div>

            {{-- Price + discount info --}}
            <div class="flex items-center gap-4 flex-shrink-0">
                @if ($this->selectedCount > 0)
                    <button
                        type="button"
                        wire:click="clear"
                        class="text-[10px] text-gray-400 hover:text-primary transition-colors avenir-bold uppercase tracking-widest"
                        title="{{ __('Clear selection') }}"
                    >
                        {{ __('Clear') }}
                    </button>

                    <div class="text-sm text-right avenir-bold">
                        @if ($this->discountPct > 0)
                            <span class="line-through text-gray-400 dark:text-gray-500">{{ $this->getFormattedPrice($this->rawTotal) }}</span>
                            <span class="font-bold text-black dark:text-white ml-1">{{ $this->getFormattedPrice($this->discountedTotal) }}</span>
                            <span class="ml-1 bg-primary text-white text-[10px] px-1.5 py-0.5 rounded avenir-bold uppercase">-{{ number_format($this->discountPct, 0) }}%</span>
                        @else
                            <span class="font-bold text-black dark:text-white">{{ $this->getFormattedPrice($this->rawTotal) }}</span>
                        @endif
                    </div>
                @endif

                @if ($this->nextTier)
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 hidden sm:block avenir-bold uppercase tracking-wider">
                        {{ __('Add :n more for :pct% off', [
                            'n' => (int)$this->nextTier['min_quantity'] - $this->selectedCount,
                            'pct' => $this->nextTier['discount_pct'],
                        ]) }}
                    </p>
                @endif

                <x-ui.button.default.primary
                    wire:click="addToCart"
                    :disabled="$this->selectedCount === 0"
                    class="!py-2 !px-4 !text-[10px]"
                >
                    <span wire:loading.remove wire:target="addToCart">
                        {{ __('Add bundle to cart') }} ({{ $this->selectedCount }})
                    </span>
                    <span wire:loading wire:target="addToCart">{{ __('Adding…') }}</span>
                </x-ui.button.default.primary>
            </div>
        </div>

        @if ($this->nextTier)
            <p x-show="!showSuccess" class="sm:hidden text-[10px] text-gray-500 dark:text-gray-400 mt-1 max-w-[85rem] mx-auto avenir-bold uppercase tracking-wider">
                {{ __('Add :n more for :pct% off', [
                    'n' => (int)$this->nextTier['min_quantity'] - $this->selectedCount,
                    'pct' => $this->nextTier['discount_pct'],
                ]) }}
            </p>
        @endif
    </div>
</div>
