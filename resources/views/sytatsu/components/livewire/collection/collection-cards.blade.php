<div class="flex flex-col gap-8">
    @forelse ($this->activeCollections as $collection)
        @php($activeBundle = app(\App\Services\BundleService::class)->findActiveForCollection($collection->collection))
        @if($activeBundle)
            {{-- A bundle collection doesn't get its own product grid here —
                 there's no single "add to cart" for any one of its products,
                 only the bundle tray on the collection's own page, so a grid
                 of tiles that can't really be bought from this far away
                 would be misleading. A thin, one-row call-to-action instead,
                 same visual language as the homepage hero
                 (mini-friends-hero.blade.php) but condensed — this is a
                 listing among others, not the page's main hero. --}}
            @php($bundleCollectionImage = $collection->collection->attribute_data->get('collection_image')?->getValue())
            @php($bundleCollectionImageUrl = $bundleCollectionImage ? asset('storage/' . $bundleCollectionImage) : $collection->collection->thumbnail?->getUrl('medium'))
            <div class="relative rounded-2xl shadow-md dark:shadow-slate-700 bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black overflow-hidden">
                @if($bundleCollectionImageUrl)
                    {{-- The collection's own image, flush to the card's own
                         top/left/bottom edges with no padding/margin of its
                         own. `absolute inset-y-0 left-0` against the card
                         (not `self-stretch` on a flex sibling) is deliberate:
                         with no explicit height, a flex item's `self-stretch`
                         cross-size is resolved from the *hypothetical*
                         (un-stretched) size of every item in the line first —
                         and an `<img>` with `h-full` but no definite parent
                         height falls back to its own native aspect ratio at
                         the given width for that pass. Two different source
                         photos (this collection's vs. the Clickerz CTA's)
                         have different native aspect ratios, so that
                         approach rendered each CTA at a different height —
                         confirmed via getBoundingClientRect() on both before
                         this fix. Taking the image out of flex flow entirely
                         removes that dependency: its box is always exactly
                         the card's own height × `w-28`, regardless of the
                         photo's own dimensions or the label's content. --}}
                    <div class="hidden sm:block absolute inset-y-0 left-0 w-28">
                        {{-- Same glow-behind-the-cut treatment as the
                             desktop hero image (mini-friends-hero.blade.php):
                             a soft blurred glow sits behind the image, only
                             visible through the sliver the clip-path cuts
                             away at the top-right, plus a `drop-shadow`
                             (not `box-shadow`, which ignores clip-path) on
                             the image itself so the angled seam reads as an
                             actual edge. --}}
                        <div class="absolute inset-y-0 left-0 w-full bg-white/40 dark:bg-white/10 blur-2xl"></div>
                        <img src="{{ $bundleCollectionImageUrl }}" alt="{{ $collection->getName() }}" class="absolute inset-0 w-full h-full object-cover" style="clip-path: polygon(0% 0%, 88% 0%, 100% 100%, 0% 100%); filter: drop-shadow(6px 0 14px rgba(0, 0, 0, 0.35));">
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-6 lg:px-12">
                    <div class="{{ $bundleCollectionImageUrl ? 'sm:ml-28' : '' }}">
                        <p class="text-xs font-bold uppercase tracking-wide text-white/80">{{ __('Bundle deal') }}</p>
                        <p class="text-xl avenir-bold text-white uppercase">{{ $collection->getName() }}</p>
                    </div>

                    <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->collection) }}" class="shrink-0 text-center px-6 py-2.5 bg-white dark:bg-primary-dark text-primary dark:text-white avenir-bold hover:bg-gray-100 dark:hover:bg-primary font-bold rounded-xl transition-colors shadow-lg text-sm">
                        {{ __('Create your bundle') }}
                    </a>
                </div>
            </div>
        @else
        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
            <div class="divide-y divide-gray-200 dark:divide-gray-500">
                <div class="group flex flex-row justify-between items-center pb-8">
                    @if($showMore)
                        <a class="text-2xl avenir-bold text-black dark:text-white hover:underline uppercase" href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->collection) }}">
                            {{ $collection->getName() }}
                        </a>
                        <a class="font-mono text-[10px] tracking-[.16em] uppercase text-secondary" href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->collection) }}">
                            <span>{{ __('Show more') }}</span>
                            <i class="fa fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    @else
                        <span class="text-2xl avenir-bold text-black dark:text-white">
                            {{ $collection->getName() }}
                        </span>
                    @endif
                </div>

                <div class="pt-8 grid {{ $gridColumns }} gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
                    @foreach($collection->products as $product)
                        <livewire:sytatsu.components.product.product-tile :product="$product" :wire:key="'product-'.$product->id.'-'.md5($product->updated_at)" />
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    @empty
        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                {{ __('No products found') }}
            </p>
        </div>
    @endforelse
</div>
