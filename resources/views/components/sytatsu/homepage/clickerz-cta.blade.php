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
<div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-6 lg:px-12">
        {{-- Image + label grouped as one flex item so `justify-between`
             still only splits the row into "this cluster" and the
             button, instead of spreading three top-level items evenly
             and pulling the image away from its label. --}}
        <div class="flex items-center gap-4 sm:gap-4">
            {{-- The photo, at the start of the row rather than between the
                 label and the button. Fixed width (`w-28 lg:w-40`, so the
                 crop's horizontal framing stays the same everywhere) but
                 `self-stretch` + `-my-6` for height, so it bleeds flush to
                 the card's top/bottom edges with no padding/margin of its
                 own instead of sitting inset within the row's own
                 vertical padding; `-ml-6 lg:-ml-12` does the same on the
                 left. `object-cover` fills that box, cropping the source
                 image as needed rather than letterboxing it. Clipped to
                 the card's rounded corners by the card's own
                 `overflow-hidden`. Hidden on mobile, where the row
                 already stacks label-above-button; a third stacked
                 element pushed the button down further than this
                 banner's "thin" purpose intends — same call made for the
                 bundle CTA's own image in collection-cards.blade.php. --}}
            <div class="hidden sm:block relative shrink-0 self-stretch -my-6 -ml-6 lg:-ml-12 w-28 lg:w-40">
                <img src="{{ Vite::asset('resources/images/banners/p1020972-clickerz-keycaps.jpg') }}" alt="{{ __('Clickerz Bar') }}" class="relative z-10 w-full h-full object-cover" style="clip-path: polygon(0% 0%, 88% 0%, 100% 100%, 0% 100%);">
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <span class="text-3xl" aria-hidden="true">🎮</span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-white/80">{{ __('Custom build') }}</p>
                    <p class="text-xl avenir-bold text-white uppercase">{{ __('Clickerz Bar') }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('sytatsu.webstore.clickerz-bar-builder') }}" class="shrink-0 text-center px-6 py-2.5 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm">
            {{ __('Start Building') }}
        </a>
    </div>
</div>
