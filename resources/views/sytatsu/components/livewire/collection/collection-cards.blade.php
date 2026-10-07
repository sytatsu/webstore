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
            <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-linear-to-br from-primary to-primary-dark dark:from-slate-900 dark:to-black overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-6 lg:px-12">
                    {{-- Image + label grouped as one flex item so `justify-between`
                         still only splits the row into "this cluster" and the
                         button, instead of spreading three top-level items
                         evenly and pulling the image away from its label. --}}
                    <div class="flex items-center gap-4 sm:gap-4">
                        @if($bundleCollectionImageUrl)
                            {{-- The collection's own image, at the start of the
                                 row rather than between the label and the
                                 button. A fixed width/height box — not
                                 `self-stretch`, which made the rendered size
                                 follow the row's own (variable) content
                                 height — so the crop stays the same
                                 landscape rectangle everywhere regardless of
                                 label length or breakpoint; `object-cover`
                                 fills that exact box, cropping the source
                                 image as needed rather than letterboxing it.
                                 `-ml-6 lg:-ml-12` cancels the row's own
                                 horizontal padding so it still bleeds flush
                                 to the card's left edge. Hidden on mobile,
                                 where the row already stacks
                                 label-above-button; a third stacked element
                                 pushed the button down further than this
                                 banner's "thin" purpose intends. --}}
                            <div class="hidden sm:block relative shrink-0 -ml-6 lg:-ml-12 w-28 h-16 lg:w-40 lg:h-20">
                                <img src="{{ $bundleCollectionImageUrl }}" alt="{{ $collection->getName() }}" class="relative z-10 w-full h-full object-cover" style="clip-path: polygon(0% 0%, 88% 0%, 100% 100%, 0% 100%);">
                            </div>
                        @endif

                        <div class="flex items-center gap-3 sm:gap-4">
                            <span class="text-3xl" aria-hidden="true">🎁</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-white/80">{{ __('Bundle deal') }}</p>
                                <p class="text-xl avenir-bold text-white uppercase">{{ $collection->getName() }}</p>
                            </div>
                        </div>
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
