{{-- Fixed to the bottom of the viewport, not sticky under the site header
     — this is the only place a bundle can be reviewed/completed/edited, so
     it must stay visible while scrolling, and anchoring it to the bottom
     keeps it clear of the header instead of competing for that same edge.

     `fixed` takes the bar out of the page's normal layout flow, so a
     dedicated `#bundle-tray-spacer` element — in the layout itself
     (sytatsu-layout.blade.php), right before the footer, not a sibling
     here — reserves the same height (tracked via a ResizeObserver on this
     root element). It lives in the layout rather than beside this
     component because this component is mounted near the *top* of the
     page (right after the page title), but the tray itself renders at
     the *bottom* of the viewport; a spacer reserving space at the mount
     point just left a tray-sized hole up top instead of protecting the
     page's actual bottom edge (the footer) from sitting underneath the
     tray. --}}
<div
    x-data="{}"
    x-init="
        const spacer = document.getElementById('bundle-tray-spacer');
        const update = () => { if (spacer) spacer.style.height = `${$el.offsetHeight}px`; };
        update();
        if (window.ResizeObserver) {
            new ResizeObserver(update).observe($el);
        } else {
            window.addEventListener('resize', update);
        }
    "
    class="fixed inset-x-0 bottom-0 z-40 sm:px-4 sm:pb-4"
>
    {{-- Edge-to-edge and square-cornered on mobile, where every pixel
         of width and height is scarce — it floats free of the
         viewport edges with its usual rounded corners and border only
         once there's room for that on larger screens. The bottom
         padding adds the device's safe-area inset (notch/home-bar) on
         top of the mobile p-3 so the content never sits under it. --}}
    <div class="mx-auto max-w-[85rem] rounded-t-2xl sm:rounded-2xl border-t-2 sm:border-2 border-primary/40 bg-white dark:bg-slate-800 shadow-[0_-8px_24px_-6px_rgba(0,0,0,0.25)] sm:shadow-lg p-3 sm:p-4"
         style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));"
         :class="pulsing ? 'animate-bundle-pulse' : ''"
         x-data="{ infoOpen: false, pulsing: false, collapsed: false }"
         {{-- One-shot feedback that something was actually added — see
              AddToCart::addToBundle()'s own dedicated `bundle-item-added`
              event (deliberately separate from `bundle-item-picked`,
              which also fires on every decrement/removal). Re-triggering
              the animation on a quick double-pick needs the class
              removed and re-added on the next tick, not just left at
              `true` — toggling a CSS class that's already applied does
              nothing, it has to actually change. This is also the only
              feedback a collapsed-on-mobile tray gives that something was
              actually added — the pulse plays on this whole card, header
              included, so it's visible whether the picked-items section
              below is expanded or collapsed. --}}
         x-on:bundle-item-added.window="pulsing = false; $nextTick(() => { pulsing = true; setTimeout(() => pulsing = false, 700); })"
    >
        <x-ui.spinner-overlay wire:loading.flex wire:target="addToCart, removeItem, clearSelection" />

        <div class="flex items-center justify-between gap-2 mb-1.5 sm:mb-2">
            <p class="text-xs avenir-bold uppercase tracking-widest text-primary">
                🎁 {{ $bundle->getTranslatedName() ?: __('Build your bundle') }}
            </p>

            <div class="flex items-center gap-1 shrink-0">
                <button type="button" @click="infoOpen = true" class="inline-flex items-center gap-x-1 text-xs font-semibold text-primary hover:underline">
                    <i class="fa fa-circle-info"></i>
                    <span>{{ __('How does this work?') }}</span>
                </button>

                @unless($added)
                    {{-- Mobile only — the tray always sits fully expanded
                         on larger screens (see the `sm:flex` overrides
                         below), so there's nothing to toggle there. This
                         is what makes the mobile-compactness goal actually
                         work while picking from a long grid: the bar can
                         shrink to just this header row instead of always
                         costing a fixed slice of a small viewport. --}}
                    <button type="button" @click="collapsed = !collapsed" class="sm:hidden inline-flex items-center justify-center size-6 rounded-full text-primary hover:bg-primary/10"
                            :aria-expanded="(!collapsed).toString()" aria-controls="bundle-tray-content"
                    >
                        <span class="sr-only">{{ __('Toggle bundle details') }}</span>
                        <i class="fa fa-chevron-up text-xs transition-transform duration-200" :class="collapsed ? 'rotate-180' : ''"></i>
                    </button>
                @endunless
            </div>
        </div>

        @if($added)
            {{-- Not subject to `collapsed` — this confirmation is the
                 whole point of the tray at this moment, so it always
                 renders in full regardless of whatever collapse state the
                 picking view was left in. --}}
            <div class="flex items-center justify-center py-1.5 sm:py-2">
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
            {{-- Collapsed-on-mobile summary — same picked-count/price/CTA
                 a visitor needs to actually finish the bundle, just
                 without the thumbnails/prompt that cost the most height.
                 `sm:hidden` keeps it out of the DOM's visible flow on
                 larger screens entirely (where `collapsed` is never true
                 regardless of this element — see the full content block
                 below), so there's no duplicate "Add bundle to cart"
                 button competing for tab order there. --}}
            <div class="sm:hidden items-center justify-between gap-2" :class="collapsed ? 'flex' : 'hidden'">
                <p class="text-xs text-gray-600 dark:text-gray-300 min-w-0 truncate">
                    <span class="avenir-bold">{{ $this->totalQuantity }}</span> {{ __('picked') }}
                    @if($this->currentTier)
                        &middot; <span class="avenir-bold text-black dark:text-white">{{ $this->formatPrice($this->currentTier->price->value * $this->totalQuantity) }}</span>
                    @endif
                </p>

                <button type="button"
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        class="shrink-0 px-3.5 py-1.5 rounded-lg text-sm font-semibold text-white bg-primary hover:opacity-90 transition-opacity disabled:opacity-50"
                >
                    {{ $editingLineId ? __('Save') : __('Add to cart') }}
                </button>
            </div>

            <div id="bundle-tray-content" class="flex-col sm:flex-row sm:items-center gap-2 sm:gap-4" :class="collapsed ? 'hidden sm:flex' : 'flex'">
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
                                 *first* row regardless of wrapping. Thumbnails
                                 shrink on mobile (size-11 vs size-14) since
                                 this bar now sits over page content rather
                                 than pushing it down, so extra rows here cost
                                 more than they used to. --}}
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-1.5 sm:pt-2 pb-0.5 sm:pb-1">
                                @foreach($this->selectedItems as $item)
                                    {{-- The tooltip bubble below is `position: fixed`, positioned
                                         from this element's own getBoundingClientRect() on hover,
                                         rather than a normal `absolute` tooltip — this row is
                                         `overflow-x-auto`, which (see the padding note above)
                                         forces its own overflow-y to clip anything poking outside
                                         its box too. `fixed` escapes that clipping entirely since
                                         its containing block is the viewport, not this row. --}}
                                    <div class="group relative size-11 sm:size-14 rounded-md flex-shrink-0 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900"
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

                            @if($this->totalQuantity > 0)
                                &middot;
                                <button type="button" wire:click="clearSelection" class="text-primary font-semibold hover:underline">
                                    {{ __('Clear bundle') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                        @if($editingLineId)
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Editing bundle in cart') }}</span>
                        @endif

                        {{-- The whole bundle's cost — quantity x the matched
                             tier price — not just the per-item price above,
                             which shows the picker how much they're actually
                             about to pay before adding it to cart. --}}
                        @if($this->currentTier)
                            <p class="text-base sm:text-lg avenir-bold text-black dark:text-white whitespace-nowrap">
                                {{ $this->formatPrice($this->currentTier->price->value * $this->totalQuantity) }}
                            </p>
                        @endif

                        <button type="button"
                                wire:click="addToCart"
                                wire:loading.attr="disabled"
                                class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm font-semibold text-white bg-primary hover:opacity-90 transition-opacity disabled:opacity-50"
                        >
                            {{ $editingLineId ? __('Save changes') : __('Add bundle to cart') }}
                        </button>
                    </div>
                </div>

                @if(!empty($bundleErrors))
                    <ul class="mt-1.5 sm:mt-2 text-xs text-red-600 dark:text-red-400 list-disc list-inside">
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

