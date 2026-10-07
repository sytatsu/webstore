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
                    <p class="text-base md:text-xl text-white/90 mb-0 font-medium">
                        {{ __('Mix and match any mini figure into one bundle and pay less per item the more you pick.') }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-start gap-4">
                    <a href="{{ $collectionRoute }}" class="px-6 py-2.5 md:px-8 md:py-3 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm md:text-base">
                        {{ __('Shop the bundle') }}
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
            <div class="relative hidden md:block md:w-1/3 lg:w-2/5">
            </div>
        </div>

        {{-- Desktop: the banner photo bleeding off the edge, same placement
             main-hero.blade.php and clickerz-hero.blade.php both use for
             their own feature image — a dense, edge-to-edge photo of the
             mini figures themselves, so object-cover never risks cropping
             into empty space or a single subject. --}}
        <div class="hidden md:block absolute top-1/2 md:-right-12 lg:-right-16 -translate-y-1/2 z-30 w-[320px] lg:w-[420px] aspect-square animate-fade-in-right" style="animation-delay: 200ms;">
            <div class="absolute inset-0 bg-white/60 dark:bg-white/20 rounded-full blur-3xl scale-110"></div>
            <img src="{{ Vite::asset('resources/images/banners/P1020940_square.jpg') }}"
                 alt="{{ $collectionName }}"
                 class="relative z-10 w-full h-full object-cover rounded-2xl shadow-2xl"
            >
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-64 h-64 bg-white/10 rounded-full blur-3xl z-10"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl z-10"></div>
    </div>
</div>
