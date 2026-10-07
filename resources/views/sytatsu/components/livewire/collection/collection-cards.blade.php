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
            {{-- Two inline PHP statements, kept in the same one-line
                 parenthesized style as the active-bundle lookup just
                 above — writing a literal "at-php" with parens inside
                 a Blade comment (even just to describe the convention)
                 made the compiler pick it up as a real directive and
                 garble everything after it, so this note is phrased
                 without that literal token on purpose. --}}
            @php($bundleCollectionImage = $collection->collection->attribute_data->get('collection_image')?->getValue())
            @php($bundleCollectionImageUrl = $bundleCollectionImage ? asset('storage/' . $bundleCollectionImage) : $collection->collection->thumbnail?->getUrl('medium'))
            <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-6 lg:px-12">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <span class="text-3xl" aria-hidden="true">🎁</span>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-white/80">{{ __('Bundle deal') }}</p>
                            <p class="text-xl avenir-bold text-white uppercase">{{ $collection->getName() }}</p>
                        </div>
                    </div>

                    @if($bundleCollectionImageUrl)
                        {{-- The collection's own image, worked into the middle
                             of this thin CTA row rather than left out
                             entirely — angled on both sides (ticket-stub
                             style) so it reads as a deliberate accent
                             rather than a stray product photo bleeding
                             into the gradient. Hidden on mobile, where
                             the row already stacks label-above-button;
                             a third stacked element pushed the button
                             down further than this banner's "thin"
                             purpose intends — same call made for the
                             image on mini-friends-hero.blade.php there. --}}
                        <div class="hidden sm:block relative shrink-0 w-24 h-16 lg:w-28 lg:h-20">
                            <div class="absolute inset-0 bg-white/25 blur-xl rounded-full"></div>
                            <img src="{{ $bundleCollectionImageUrl }}" alt="{{ $collection->getName() }}" class="relative z-10 w-full h-full object-cover rounded-sm" style="clip-path: polygon(10% 0%, 100% 0%, 90% 100%, 0% 100%); filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.35));">
                        </div>
                    @endif

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
