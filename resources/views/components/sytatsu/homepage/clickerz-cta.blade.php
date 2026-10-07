{{-- Thin, one-row CTA for the Clickerz Bar Builder — same visual
     language as the bundle CTA a bundle collection gets in the homepage
     listing (collection-cards.blade.php), reused here since the Bar
     Builder isn't a collection and so never passes through that
     component. Its position among the featured collections is admin-
     configured (HomeFeaturedCollectionsSettingsPage), gated on the Bar
     Builder being enabled and the hero not already being the Clickerz
     hero (clickerz-hero.blade.php) — stacking two Clickerz promos
     directly on top of each other would be redundant; see
     Welcome.php's getHomepageElementsAttribute() for that check. No
     own bottom margin — it sits inside welcome.blade.php's `gap-8`
     list alongside the collection rows, same as every sibling there. --}}
<div class="relative rounded-2xl shadow-md dark:shadow-slate-700 bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black overflow-hidden">
    {{-- The photo, flush to the card's own top/left/bottom edges with no
         padding/margin of its own. `absolute inset-y-0 left-0` against
         the card (not `self-stretch` on a flex sibling) is deliberate:
         with no explicit height, a flex item's `self-stretch` cross-size
         is resolved from the *hypothetical* (un-stretched) size of every
         item in the line first — and an `<img>` with `h-full` but no
         definite parent height falls back to its own native aspect ratio
         at the given width for that pass. This photo and the bundle
         CTA's own (collection-cards.blade.php) have different native
         aspect ratios, so that approach rendered each CTA at a different
         height — confirmed via getBoundingClientRect() on both before
         this fix. Taking the image out of flex flow entirely removes
         that dependency: its box is always exactly the card's own
         height × `w-28`, regardless of the photo's own dimensions or
         the label's content. --}}
    <div class="hidden sm:block absolute inset-y-0 left-0 w-28">
        <img src="{{ Vite::asset('resources/images/banners/p1020972-clickerz-keycaps.jpg') }}" alt="{{ __('Clickerz Bar') }}" class="absolute inset-0 w-full h-full object-cover" style="clip-path: polygon(0% 0%, 88% 0%, 100% 100%, 0% 100%);">
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-6 lg:px-12">
        <div class="flex items-center gap-3 sm:gap-4 sm:ml-28">
            <span class="text-3xl" aria-hidden="true">🎮</span>
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-white/80">{{ __('Custom build') }}</p>
                <p class="text-xl avenir-bold text-white uppercase">{{ __('Clickerz Bar') }}</p>
            </div>
        </div>

        <a href="{{ route('sytatsu.webstore.clickerz-bar-builder') }}" class="shrink-0 text-center px-6 py-2.5 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm">
            {{ __('Start Building') }}
        </a>
    </div>
</div>
