<div class="group flex flex-col">
    <div class="relative">
        <livewire:sytatsu.components.product.carousel :product="$this->product" :images="$this->product->images" />

        <a class="flex flex-col" href="{{ \App\Services\WebstoreHelperService::getProductRoute($this->product) }}">
            <div class="pt-4 [&>*]:hover:underline">
                <h3 class="font-medium md:text-lg text-black dark:text-white">
                    {{ $this->product->translateAttribute('name') }}
                </h3>

                <p class="mt-2 font-semibold text-black dark:text-white">
                    {{ $this->getPriceRangeString() }}
                </p>
            </div>
        </a>
    </div>

    <div class="mb-2 mt-4 text-sm">
        <div class="flex flex-col">
            {{-- TODO; Every line/item should be it's own component --}}
            @foreach($this->getProductOptionsArray() as $optionCollectionName => $options)
                <div class="py-3 border-t border-gray-200 dark:border-neutral-700">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-medium text-black dark:text-white">{{ $optionCollectionName }}:</span>
                        </div>

                        <div class="text-end text-black dark:text-white">
                            @foreach($options as $option)
                                <span >{{ $option['name'] }}</span>{{ !$loop->last ? ', ' : '' }}
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
                        @foreach($this->product->collections as $collection)
                            <div class="block">
                                @if ($collection->parent)
                                    <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->parent) }}" class="hover:underline text-nowrap">{{ $collection->parent->translateAttribute('name') }}</a><span><i class="px-1 fa fa-caret-right"></i></span>
                                @endif

                                <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection) }}" class="hover:underline text-nowrap">{{ $collection->translateAttribute('name') }}</a>
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
           {{-- @TODO; Disabled state when out of stock --}}
           href="#bag_{{ $this->product->id }}">{{ __('Add to Cart') }}<i class="fa fa-cart-shopping"></i>
        </a>
    </div>
</div>
