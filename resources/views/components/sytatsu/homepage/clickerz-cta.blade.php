{{-- Thin, one-row CTA for the Clickerz Bar Builder — same visual
     language as the bundle CTA a bundle collection gets in the homepage
     listing (collection-cards.blade.php), reused here since the Bar
     Builder isn't a collection and so never passes through that
     component. Shown below the hero whenever the Bar Builder is
     enabled, except when the hero itself is already the Clickerz hero
     (clickerz-hero.blade.php) — stacking two Clickerz promos directly
     on top of each other would be redundant; see welcome.blade.php for
     that check. --}}
<div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black overflow-hidden mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-6 lg:px-12">
        <div class="flex items-center gap-3 sm:gap-4">
            <span class="text-3xl" aria-hidden="true">🎮</span>
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-white/80">{{ __('Custom build') }}</p>
                <p class="text-xl avenir-bold text-white uppercase">{{ __('Clickerz Bar') }}</p>
            </div>
        </div>

        <div class="hidden sm:block relative shrink-0 w-24 h-16 lg:w-28 lg:h-20">
            <div class="absolute inset-0 bg-white/25 blur-xl rounded-full"></div>
            <img src="{{ Vite::asset('resources/images/seeders/clickerz-bar-hero.svg') }}" alt="{{ __('Clickerz Bar') }}" class="relative z-10 w-full h-full object-contain drop-shadow-lg -rotate-3">
        </div>

        <a href="{{ route('sytatsu.webstore.clickerz-bar-builder') }}" class="shrink-0 text-center px-6 py-2.5 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm">
            {{ __('Start Building') }}
        </a>
    </div>
</div>
