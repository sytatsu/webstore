<div class="mx-auto xl:min-w-[80rem] max-w-[85rem] px-4 sm:px-6 lg:px-8 py-12 lg:py-24 flex flex-col md:grid md:grid-cols-2 xl:grid-cols-3 gap-6 md:gap-12">
    <livewire:sytatsu.components.product.carousel :product="$product" :carouselType="\App\Enums\CarouselTypeEnum::EXPANDED" :images="$product->images" />

    <div class="xl:col-span-2 flex flex-col text-sm gap-3">
        <div class="flex flex-col-reverse md:flex-row justify-between">
            <h3 class="font-medium text-lg md:text-xl text-black dark:text-white">
                {{ $product->translateAttribute('name') }}
            </h3>
            <div class="flex-nowrap text-black dark:text-white my-auto">
                @foreach($product->collections as $collection)
                    @if ($collection->parent)
                        <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->parent) }}" class="hover:underline text-nowrap">{{ $collection->parent->translateAttribute('name') }}</a><span><i class="px-1 fa fa-caret-right"></i></span>
                    @endif

                    <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection) }}" class="hover:underline text-nowrap">{{ $collection->translateAttribute('name') }}</a>
                @endforeach
            </div>
        </div>

        <hr class="text-gray-500 w-12" />

        <div class="font-light text-black dark:text-white">
                {!! __($product->translateAttribute('description')) !!}
        </div>

        <div class="pb-4">
            <div id="hs-show-hide-collapse-heading" class="hs-collapse hidden flex flex-col gap-2 mb-2 w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-show-hide-collapse">

                {{-- @TODO: create foreach loop for specifications --}}
                <hr class="text-gray-500"/>
                <div class="grid grid-cols-2 gap-2">
                    <span class="font-medium text-black dark:text-white">{{ __('Material') }}:</span>
                    <span class="text-end text-black dark:text-white">{{ __('PLA') }}</span>
                </div>

                {{-- @TODO; Only on last in loop --}}
                <hr class="text-gray-500"/>
            </div>
            <div class="flex justify-end">
                <button type="button" class="hs-collapse-toggle inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-transparent text-primary-dark decoration-2 hover:text-primary hover:underline focus:outline-hidden focus:underline focus:text-primary disabled:opacity-50 disabled:pointer-events-none dark:text-primary dark:hover:text-primary-light dark:focus:text-primary-light" id="hs-show-hide-collapse" aria-expanded="false" aria-controls="hs-show-hide-collapse-heading" data-hs-collapse="#hs-show-hide-collapse-heading">
                    <span class="hs-collapse-open:hidden">{{ __('Show specifications') }}</span>
                    <span class="hs-collapse-open:block hidden">{{ __('Hide specifications') }}</span>
                    <svg class="hs-collapse-open:rotate-180 shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex flex-col space-y-6 mt-auto">
            <div class="grid grid-cols-4 grid-flow-row-dense gap-2">
                @foreach ($this->productOptions as $option)
                    <span class="pr-2 self-center font-medium text-gray-700 dark:text-white border-r border-gray-500 col">
                        {{ __($option['option']->translate('name')) }}
                    </span>

                    <div class="flex flex-grow flex-wrap flex-row-reverse gap-2 text-xs tracking-wide uppercase col-span-3"
                         x-data="{selectedOption: @entangle('selectedOptionValues'),selectedValues: []}"
                         x-init="selectedValues = Object.values(selectedOption); $watch('selectedOption', value => selectedValues = Object.values(selectedOption))"
                    >
                        @foreach ($option['values'] as $value)
                            <button class="px-3 py-2 font-medium rounded-lg outline-none focus:ring"
                                    type="button"
                                    wire:click="setSelectedOptionValue('{{ $option['option']->id }}', {{ $value->id }})"
                                    :class="{'!bg-primary-dark text-white ': selectedValues.includes({{ $value->id }}), 'border border-gray-100 dark:border-slate-900 bg-white hover:bg-gray-100 dark:bg-slate-900 hover:dark:bg-slate-800 text-black dark:text-white cursor-pointer': !selectedValues.includes({{ $value->id }})}">
                                {{ __($value->translate('name')) }}
                            </button>
                        @endforeach
                    </div>

                    @if (!$loop->last)
                        <hr class="mx-1 col-span-4 text-gray-500" />
                    @endif
                @endforeach
            </div>

            <livewire:sytatsu.components.add-to-cart :purchasable="$this->variant" :wire:key="$this->variant->id" />
        </div>
    </div>
</div>
