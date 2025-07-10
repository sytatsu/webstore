<div class="mx-auto max-w-[85rem] px-4 sm:px-6 lg:px-8 py-12 lg:py-24 grid-cols-1 md:grid-cols-2 md:grid xl:grid-cols-3 gap-12">
        <livewire:sytatsu.components.product.carousel :product="$product" :carouselType="\App\Enums\CarouselTypeEnum::EXPANDED" :images="$product->images" />

        <div class="xl:col-span-2 flex flex-col mb-2 mt-4 text-sm">
            <a class="flex flex-col" href="{{ \App\DataTransformers\RouteTransformer::getProductRoute($product) }}">
                <div class="pt-4 [&>*]:hover:underline">
                    <h3 class="font-medium md:text-lg text-black dark:text-white">
                        {{ $product->translateAttribute('name') }}
                    </h3>

                    <p class="mt-2 font-semibold text-black dark:text-white">
                        {{ $this->getPriceRangeString() }}
                    </p>
                </div>
            </a>


            @foreach($this->getProductOptionsArray() as $optionCollectionName => $options)
                <div class="py-3 border-t border-gray-200 dark:border-neutral-700">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-medium text-black dark:text-white">{{ $optionCollectionName }}:</span>
                        </div>

                        <div class="text-end text-black dark:text-white">
                            @foreach($options as $option)
                                <a href="{{ \App\DataTransformers\RouteTransformer::getProductRoute($product, ['option_id' => $option['id']]) }}" class="hover:underline">{{ $option['name'] }}</a>{{ !$loop->last ? ', ' : '' }}
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
                                    <a href="{{ \App\DataTransformers\RouteTransformer::getCollectionRoute($collection->parent) }}" class="hover:underline text-nowrap">{{ $collection->parent->translateAttribute('name') }}</a><span><i class="px-1 fa fa-caret-right"></i></span>
                                @endif

                                <a href="{{ \App\DataTransformers\RouteTransformer::getCollectionRoute($collection) }}" class="hover:underline text-nowrap">{{ $collection->translateAttribute('name') }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-auto">
                <a class="py-2 px-3 w-full inline-flex justify-center items-center gap-x-2 text-sm font-medium text-nowrap rounded-xl border border-transparent bg-primary-dark text-white hover:bg-primary focus:outline-hidden focus:bg-primary-dark transition disabled:opacity-50 disabled:pointer-events-none"
                   href="#bag_{{ $product->id }}">{{ __('Add to Cart') }}<i class="fa fa-cart-shopping"></i>
                </a>
            </div>
        </div>



</div>
