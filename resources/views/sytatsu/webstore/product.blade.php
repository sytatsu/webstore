<div class="mx-auto xl:min-w-[80rem] max-w-[85rem] px-4 sm:px-6 lg:px-8 py-12 lg:py-24 grid-cols-1 md:grid-cols-2 md:grid xl:grid-cols-3 gap-12">
    <livewire:sytatsu.components.product.carousel :product="$product" :carouselType="\App\Enums\CarouselTypeEnum::EXPANDED" :images="$product->images" />

    <div class="xl:col-span-2 flex flex-col text-sm">
        <h3 class="font-medium text-lg md:text-xl text-black dark:text-white">
            {{ $product->translateAttribute('name') }}
        </h3>

        <div class="py-3 border-t border-gray-200 dark:border-neutral-700 font-light text-black dark:text-white">
                {!! __($product->translateAttribute('description')) !!}
        </div>

{{--        @TODO; Might want to so something with this in the neat future --}}
{{--        <div class="py-3 border-t border-gray-200 dark:border-neutral-700">--}}
{{--            <div class="grid grid-cols-2 gap-2">--}}
{{--                <div>--}}
{{--                    <span class="font-medium text-black dark:text-white">{{ __('Collection') }}:</span>--}}
{{--                </div>--}}

{{--                <div class="text-end text-black dark:text-white">--}}
{{--                    @foreach($product->collections as $collection)--}}
{{--                        <div class="block">--}}
{{--                            @if ($collection->parent)--}}
{{--                                <a href="{{ \App\DataTransformers\RouteTransformer::getCollectionRoute($collection->parent) }}" class="hover:underline text-nowrap">{{ $collection->parent->translateAttribute('name') }}</a><span><i class="px-1 fa fa-caret-right"></i></span>--}}
{{--                            @endif--}}

{{--                            <a href="{{ \App\DataTransformers\RouteTransformer::getCollectionRoute($collection) }}" class="hover:underline text-nowrap">{{ $collection->translateAttribute('name') }}</a>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="flex flex-col space-y-6 mt-auto">
            <div class="grid grid-cols-4 grid-flow-row-dense gap-2">
                @foreach ($this->productOptions as $option)
                    <span class="pr-2 self-center font-medium text-gray-700 dark:text-white border-r border-slate-800 dark:border-gray-100 col">
                        {{ __($option['option']->translate('name')) }}
                    </span>

                    <div class="flex flex-grow flex-wrap flex-row-reverse gap-2 text-xs tracking-wide uppercase col-span-3"
                         x-data="{selectedOption: @entangle('selectedOptionValues'),selectedValues: []}"
                         x-init="selectedValues = Object.values(selectedOption); $watch('selectedOption', value => selectedValues = Object.values(selectedOption))">
                        @foreach ($option['values'] as $value)
                            <button class="px-3 py-2 font-medium rounded-lg outline-none focus:ring"
                                    type="button"
                                    wire:click="$set('selectedOptionValues.{{ $option['option']->id }}', {{ $value->id }})"
                                    :class="{'!bg-primary-dark cursor-none text-white ': selectedValues.includes({{ $value->id }}), 'border border-gray-100 dark:border-slate-900 bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 text-black dark:text-white cursor-pointer': !selectedValues.includes({{ $value->id }})}">
                                {{ __($value->translate('name')) }}
                            </button>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <livewire:sytatsu.components.add-to-cart :purchasable="$this->variant" :wire:key="$this->variant->id" />
        </div>
    </div>
</div>
