<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
    <!-- Card Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
        @foreach($this->getProducts() as $product)
            {{-- @TODO; Whole section should be it's own component --}}
            <div class="group flex flex-col">
                <div class="relative">
                    <div class="aspect-square overflow-hidden rounded-2xl">

                        {{-- @TODO; Make separate component from the carousel --}}
                        <div data-hs-carousel='{
                            "loadingClasses": "opacity-0",
                            "dotsItemClasses": "hs-carousel-active:bg-primary hs-carousel-active:border-white size-3 border border-neutral-200 rounded-2xl cursor-pointer dark:border-neutral-200 dark:hs-carousel-active:bg-primary dark:hs-carousel-active:border-white",
                            {{-- @TODO; isDraggable feels a bit buggy, see if we can fix this --}}
                            "isDraggable": true
                        }' class="relative">
                            <div class="hs-carousel relative overflow-hidden w-full min-h-96 bg-white rounded-lg">
                                {{-- @TODO; Make sure the link is created to the product page --}}
                                <a class="flex flex-col hover:underline" href="#view_{{ $product->id }}">
                                    <div
                                        class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 opacity-0">
                                        @forelse($product->images->sortByDesc(fn (\Spatie\MediaLibrary\MediaCollections\Models\Media $image) => $image->custom_properties['primary']) as $image)
                                            <div class="hs-carousel-slide">
                                                <img class="size-full object-cover aspect-square transform transition-all scale-100 hover:scale-105"
                                                     src="{{ $image->getFullUrl() }}"
                                                     alt="{{ $image->name }}">
                                            </div>
                                        @empty
                                            {{-- @TODO; Placeholder image should be replaced, now image from outdated logo --}}
                                            <div class="hs-carousel-slide">
                                                <img class="size-full object-cover aspect-square transform transition-all scale-100 hover:scale-105"
                                                     src="{{ Vite::asset('resources/images/product_placeholder.jpg') }}"
                                                     alt="product_placeholder">
                                            </div>
                                        @endforelse
                                    </div>
                                </a>

                                @if ($product->images->count() > 1)
                                    <button type="button"
                                            class="hs-carousel-prev hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute px-2 inset-y-0 start-0 inline-flex justify-center items-center w-11.5 h-full text-gray-800 hover:bg-gray-800/10 focus:outline-hidden focus:bg-gray-800/10 rounded-s-lg dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10">
                                        <span class="text-2xl" aria-hidden="true">
                                          <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                               fill="none" stroke="currentColor"
                                               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6"></path>
                                          </svg>
                                        </span>
                                        <span class="sr-only">Previous</span>
                                    </button>
                                    <button type="button"
                                            class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute px-2 inset-y-0 end-0 inline-flex justify-center items-center w-11.5 h-full text-gray-800 hover:bg-gray-800/10 focus:outline-hidden focus:bg-gray-800/10 rounded-e-lg dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10">
                                        <span class="sr-only">Next</span>
                                        <span class="text-2xl" aria-hidden="true">
                                          <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                               fill="none" stroke="currentColor"
                                               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m9 18 6-6-6-6"></path>
                                          </svg>
                                        </span>
                                    </button>
                                @endif
                            </div>

                            @if ($product->images->count() > 1)
                                <div class="hs-carousel-pagination flex justify-center absolute bottom-3 start-0 end-0 flex gap-x-2"></div>
                            @endif
                        </div>
                    </div>

                    {{-- @TODO; Make sure the link is created to the product page --}}
                    <a class="flex flex-col" href="#view_{{ $product->id }}">
                        <div class="pt-4 [&>*]:hover:underline">
                            <h3 class="font-medium md:text-lg text-black dark:text-white">
                                {{ $product->translateAttribute('name') }}
                            </h3>

                            <p class="mt-2 font-semibold text-black dark:text-white">
                                {{ \App\DataTransformers\PriceTransformer::rangeString(priceCollection: $product->prices) }}
                            </p>
                        </div>
                    </a>

                </div>

                <div class="mb-2 mt-4 text-sm">
                    <div class="flex flex-col">
                        {{-- TODO; Every line/item should be it's own component --}}
                        @foreach(\App\DataTransformers\ProductTransformer::productOptionsArray(product: $product) as $optionCollectionName => $options)
                            <div class="py-3 border-t border-gray-200 dark:border-neutral-700">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <span class="font-medium text-black dark:text-white">{{ $optionCollectionName }}:</span>
                                    </div>

                                    <div class="text-end text-black dark:text-white">
                                        @foreach($options as $option)
                                            {{-- @TODO; Make sure the link is created to the product page with the correct option selected --}}
                                            <a href="#value_{{ $option['id'] }}" class="hover:underline">{{ $option['name'] }}</a>{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="py-3 border-t border-gray-200 dark:border-neutral-700">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="font-medium text-black dark:text-white">{{ __('Collection') }}:</span>
                                </div>

                                <div class="text-end text-black dark:text-white">
                                    @foreach($product->collections as $collection)
                                        <div class="block">
                                            @if ($collection->parent)
                                                {{-- @TODO; Make sure the link is created to the collection page with the correct collection --}}
                                                <a href="#collection_{{ $collection->parent->id }}" class="hover:underline">{{ $collection->parent->translateAttribute('name') }}</a><span> > </span>
                                            @endif

                                            {{-- @TODO; Make sure the link is created to the collection page with the correct collection --}}
                                            <a href="#collection_{{ $collection->id }}" class="hover:underline">{{ $collection->translateAttribute('name') }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- @TODO; Should be converted to a livewire component --}}
                <div class="flex gap-3 mt-auto">
                    <a class="py-2 px-3 w-full inline-flex justify-center items-center gap-x-2 text-sm font-medium text-nowrap rounded-xl border border-transparent bg-primary-dark text-white hover:bg-primary focus:outline-hidden focus:bg-primary-dark transition disabled:opacity-50 disabled:pointer-events-none"
                       {{-- @TODO; Make sure an event gets fired to add to shopping cart --}}
                       {{-- @TODO; Make a pop-up with option list if variants exisits --}}
                       href="bag.html">{{ __('Add to Cart') }}<i class="fa fa-cart-shopping"></i>
                    </a>
                </div>
            </div>
            <!-- End Card -->
        @endforeach
    </div>
    <!-- End Card Grid -->
</div>
