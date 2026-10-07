{{-- Always on screen, pinned under the site header rather than fixed to
     the bottom of the viewport — this is the only place a bundle can be
     reviewed/completed/edited, so it must stay visible while scrolling.
     `sticky` needs an explicit `top` matching the header's *current*
     height (it changes as the header shrinks on scroll — see
     navigation.blade.php's `scrolled` state), so a ResizeObserver on
     #site-header keeps this in sync instead of a hardcoded pixel value.

     It's also wider than the page's own content width while sitting in
     its normal (non-stuck) position, then eases down to that same width
     once scrolling has actually pinned it under the header. "Stuck" is
     read from the element's own bounding box rather than a separate
     sentinel: wrapping this in one more plain ancestor div to host that
     state broke native `position: sticky` outright (confirmed by testing
     it), so the state lives on this element itself instead.

     No horizontal padding here — the page layout (sytatsu-layout.blade.php)
     already pads its whole $slot, same as the filter/grid row below this
     one; adding more here on top of that made this narrower than that
     row instead of matching it.

     The top padding is conditional on `stuck`, not a flat `pt-4`: it's
     breathing room between this and the site header once scrolling has
     actually pinned the two together, not extra space above the title
     card above it — the page's own `gap-8` between sections already
     covers that, and stacking this padding on top of it made the gap
     above the tray bigger than every other section-to-section gap on
     the page. --}}
<div
    class="sticky z-40"
    x-data="{ top: 0, stuck: false }"
    x-init="
        const header = document.getElementById('site-header');
        const update = () => {
            top = header ? header.offsetHeight : 0;
            stuck = $el.getBoundingClientRect().top <= top;
        };
        update();
        if (header && window.ResizeObserver) {
            new ResizeObserver(update).observe(header);
        } else {
            window.addEventListener('resize', update);
        }
        window.addEventListener('scroll', update, { passive: true });
    "
    :style="`top: ${top}px`"
    :class="stuck ? 'pt-4' : ''"
>
    {{-- A solid, fully opaque fill matching every other section on the
         page (the filter/sort cards, the product grid card) — a
         gradient through a translucent primary tint used to sit here,
         which both let the page's own background bleed through at the
         tinted edge, and looked inconsistent with those other white
         cards. The border carries the "this is different" cue instead. --}}
    <div class="mx-auto rounded-2xl shadow-lg border-2 border-primary/40 bg-white dark:bg-slate-800 p-4 transition-[max-width] duration-300"
         :class="`${stuck ? 'max-w-[85rem]' : 'max-w-[95rem]'} ${pulsing ? 'animate-bundle-pulse' : ''}`"
         x-data="{ infoOpen: false, pulsing: false }"
         {{-- One-shot feedback that something was actually added — see
              AddToCart::addToBundle()'s own dedicated `bundle-item-added`
              event (deliberately separate from `bundle-item-picked`,
              which also fires on every decrement/removal). Re-triggering
              the animation on a quick double-pick needs the class
              removed and re-added on the next tick, not just left at
              `true` — toggling a CSS class that's already applied does
              nothing, it has to actually change. --}}
         x-on:bundle-item-added.window="pulsing = false; $nextTick(() => { pulsing = true; setTimeout(() => pulsing = false, 700); })"
    >
        <x-ui.spinner-overlay wire:loading.flex wire:target="addToCart, removeItem" />

        <div class="flex items-center justify-between gap-2 mb-2">
            <p class="text-xs avenir-bold uppercase tracking-widest text-primary">
                🎁 {{ $bundle->getTranslatedName() ?: __('Build your bundle') }}
            </p>

            <button type="button" @click="infoOpen = true" class="shrink-0 inline-flex items-center gap-x-1 text-xs font-semibold text-primary hover:underline">
                <i class="fa fa-circle-info"></i>
                <span>{{ __('How does this work?') }}</span>
            </button>
        </div>

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
                        {{-- flex-wrap, not overflow-x-auto — a horizontally
                             scrolling strip hid picks beyond the first
                             handful off to the side; wrapping onto more
                             rows instead means everything picked is always
                             visible at once, same as the cart's own bundle
                             line (items.blade.php). pt-2 is still kept (not
                             py-1): the quantity badge sits -top-1.5 above
                             its own thumbnail, and needs that room on the
                             *first* row regardless of wrapping. --}}
                        <div class="flex flex-wrap items-center gap-2 pt-2 pb-1">
                            @foreach($this->selectedItems as $item)
                                {{-- The tooltip bubble below is `position: fixed`, positioned
                                     from this element's own getBoundingClientRect() on hover,
                                     rather than a normal `absolute` tooltip — this row is
                                     `overflow-x-auto`, which (see the padding note above)
                                     forces its own overflow-y to clip anything poking outside
                                     its box too. `fixed` escapes that clipping entirely since
                                     its containing block is the viewport, not this row. --}}
                                <div class="group relative size-14 rounded-md flex-shrink-0 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900"
                                     title="{{ $item['name'] }}"
                                     wire:key="bundle-selected-{{ $item['variant_id'] }}"
                                     x-data="{ tipShown: false, tipX: 0, tipY: 0 }"
                                     @mouseenter="tipShown = true; const r = $el.getBoundingClientRect(); tipX = r.left + r.width / 2; tipY = r.top"
                                     @mouseleave="tipShown = false"
                                >
                                    <img src="{{ $item['thumbnail'] ?? \App\Services\WebstoreHelperService::productPlaceholderImage() }}"
                                         alt="{{ $item['name'] }}" class="w-full h-full object-contain p-1 rounded-md">

                                    <span class="absolute -top-1.5 -right-1.5 bg-primary text-white text-[9px] font-bold size-4 rounded-full flex items-center justify-center avenir-bold leading-none">
                                        {{ $item['quantity'] }}
                                    </span>

                                    <div x-show="tipShown" x-cloak
                                         class="fixed z-[100] -translate-x-1/2 -translate-y-full px-2 py-1 rounded-md bg-slate-900 text-white text-[11px] whitespace-nowrap shadow-lg pointer-events-none"
                                         :style="`left: ${tipX}px; top: ${tipY - 6}px`"
                                         style="display: none;"
                                    >{{ $item['name'] }}</div>

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
                <div class="flex items-center gap-3 shrink-0">
                    @if($editingLineId)
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Editing bundle in cart') }}</span>
                    @endif

                    {{-- The whole bundle's cost — quantity x the matched
                         tier price — not just the per-item price above,
                         which shows the picker how much they're actually
                         about to pay before adding it to cart. --}}
                    @if($this->currentTier)
                        <p class="text-lg avenir-bold text-black dark:text-white whitespace-nowrap">
                            {{ $this->formatPrice($this->currentTier->price->value * $this->totalQuantity) }}
                        </p>
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

        {{-- "How does this work?" explainer — the tiers themselves come
             straight from the bundle's own Price rows, so this can never
             drift out of sync with what checkout actually charges. --}}
        <div x-show="infoOpen"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter="transition duration-200 transform ease"
             x-transition:leave="transition duration-200 transform ease"
             x-transition:leave-end="opacity-0 scale-90"
             class="fixed inset-0 z-80 flex items-center justify-center overflow-y-auto bg-slate-900/40 backdrop-blur-sm p-4"
             style="display: none;"
             role="dialog" tabindex="-1" @keydown.escape.window="infoOpen = false"
        >
            <div @click.outside="infoOpen = false" class="relative w-full max-w-md rounded-2xl shadow-lg bg-white dark:bg-slate-800 p-6">
                <button type="button" @click="infoOpen = false" class="absolute top-3 end-3 size-8 inline-flex justify-center items-center rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-700">
                    <span class="sr-only">{{ __('Close') }}</span>
                    <i class="fa fa-xmark"></i>
                </button>

                <h3 class="text-lg avenir-bold uppercase text-black dark:text-white mb-2">
                    {{ $bundle->getTranslatedName() ?: __('Build your bundle') }}
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    {{ __('Mix and match any of the products below into one bundle — the more you pick, the less each one costs. Every item in your bundle is charged at the price for the tier your total quantity reaches.') }}
                </p>

                <ul class="divide-y divide-gray-200 dark:divide-slate-600 rounded-lg border border-gray-200 dark:border-slate-600 overflow-hidden">
                    @foreach($bundle->tiers() as $tier)
                        <li class="flex items-center justify-between px-4 py-2 text-sm">
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $tier->min_quantity <= 1
                                    ? __('1 item')
                                    : __(':count+ items', ['count' => $tier->min_quantity]) }}
                            </span>
                            <span class="avenir-bold text-black dark:text-white">
                                {{ $this->formatPrice($tier->price->value) }} {{ __('each') }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                <button type="button" @click="infoOpen = false" class="w-full mt-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-primary hover:opacity-90 transition-opacity">
                    {{ __('Got it') }}
                </button>
            </div>
        </div>
    </div>
</div>
