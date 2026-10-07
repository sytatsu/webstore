@php
    // Only one Bundle exists right now, so "the" enabled one is "the"
    // Mini-Friends bundle — same assumption HomepageHeroSettingsPage::current()
    // already makes before this component ever renders (it falls back to
    // the general hero once no bundle is left enabled, so $bundle below is
    // never actually null in practice, but the null-safe chain is cheap
    // insurance against that invariant drifting later).
    $bundle = \App\Models\Bundle::query()->enabled()->with('collection.defaultUrl')->first();
    $collectionName = $bundle?->collection?->translateAttribute('name') ?? 'Mini-Friends';
    $collectionRoute = $bundle?->collection
        ? \App\Services\WebstoreHelperService::getCollectionRoute($bundle->collection)
        : route('sytatsu.webstore.collections');
@endphp

{{-- Mini-Friends Bundle Hero --}}
<div class="relative mb-8 rounded-2xl overflow-hidden">
    <div class="relative rounded-2xl bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black shadow-md dark:shadow-slate-700">
        <div class="relative z-20 px-6 pt-12 pb-12 md:px-12 md:py-20 flex flex-col md:flex-row items-center justify-between gap-10 md:gap-12 min-h-[350px] md:min-h-[420px]">
            <div class="flex flex-col gap-6 w-full md:max-w-none md:w-3/5">
                <div class="inline-flex items-center gap-2 w-fit px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white/90 text-xs font-bold uppercase tracking-wide avenir-bold">
                    <span>🎁</span> <span class="mt-1">{{ __('Bundle deal') }}</span>
                </div>

                <div class="max-w-xl text-left">
                    <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-4 md:mb-6 avenir-bold tracking-tight">
                        {{ __('Build your own :name bundle.', ['name' => $collectionName]) }}
                    </h1>
                    {{-- The price-break mechanic itself ("...and pay less
                         per item the more you pick") was dropped here — a
                         given feature of how bundles work, not the actual
                         selling point of this hero. --}}
                    <p class="text-base md:text-xl text-white/90 mb-0 font-medium">
                        {{ __('Mix and match any mini figure into one bundle.') }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-start gap-4">
                    <a href="{{ $collectionRoute }}" class="px-6 py-2.5 md:px-8 md:py-3 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm md:text-base">
                        {{ __('Create your bundle') }}
                    </a>
                </div>
            </div>

            {{-- Mobile: the banner photo inline, below the copy (no room to
                 bleed it off the edge like the desktop version does). --}}
            <div class="relative md:hidden w-full rounded-xl overflow-hidden shadow-xl animate-fade-in-right" style="animation-delay: 200ms;">
                <img src="{{ Vite::asset('resources/images/banners/P1020940_square.jpg') }}"
                     alt="{{ $collectionName }}"
                     class="w-full aspect-[4/3] object-cover"
                >
            </div>

            <!-- Hero Image Container (Spacer for relative positioning, desktop) -->
            <div class="relative hidden md:block md:w-[42%] lg:w-[40%]">
            </div>
        </div>

        {{-- Desktop: the banner photo fills the card's full height — `inset-y-0`,
             not a fixed-size square floating mid-card like the first version,
             so there's no gap above or below it — and bleeds flush to the
             card's own right edge. The left edge is cut on an angle rather
             than left as a plain vertical line: a `clip-path` polygon for the
             cut itself, plus a `drop-shadow` (not `box-shadow`, which ignores
             clip-path and would still draw a rectangular shadow) so the angled
             seam actually reads as an edge, not a flat pasted-on rectangle.
             The soft glow sitting behind/left of it shows through that angled
             cut instead of just peeking out around a square's corners. --}}
        <div class="hidden md:block absolute inset-y-0 right-0 z-30 w-[42%] lg:w-[40%] animate-fade-in-right" style="animation-delay: 200ms;">
            <div class="absolute inset-y-0 left-0 w-2/3 bg-white/50 dark:bg-white/15 blur-3xl"></div>
            <img src="{{ Vite::asset('resources/images/banners/P1020940_square.jpg') }}"
                 alt="{{ $collectionName }}"
                 class="relative z-10 w-full h-full object-cover"
                 style="clip-path: polygon(14% 0%, 100% 0%, 100% 100%, 0% 100%); filter: drop-shadow(-12px 0 24px rgba(0, 0, 0, 0.35));"
            >
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-64 h-64 bg-white/10 rounded-full blur-3xl z-10"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl z-10"></div>
    </div>
</div>
