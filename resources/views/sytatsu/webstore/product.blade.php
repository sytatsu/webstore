<div class="mx-auto xl:min-w-[80rem] max-w-[30rem] md:max-w-[85rem] w-full flex flex-col gap-8">
    <div class="flex flex-col md:grid md:grid-cols-2 xl:grid-cols-3 gap-8 md:gap-12 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12">
        <livewire:sytatsu.components.product.carousel :product="$product" :carouselType="\App\Enums\CarouselTypeEnum::EXPANDED" :images="$product->images" />

        <div class="xl:col-span-2 flex flex-col text-sm gap-4">
            <div class="flex flex-col-reverse md:flex-row justify-between items-start md:items-center">
                <h2 class="text-2xl font-bold text-black dark:text-white avenir-bold uppercase">
                    {{ $product->translateAttribute('name') }}
                </h2>
                <div class="flex-nowrap text-black dark:text-white mb-2 md:mb-0">
                    @foreach($product->collections as $collection)
                        @if ($collection->parent)
                            <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->parent) }}" class="hover:underline text-nowrap">{{ $collection->parent->translateAttribute('name') }}</a><span><i class="px-1 fa fa-caret-right"></i></span>
                            <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection->parent, ['subCollections' => [$collection->id]]) }}" class="hover:underline text-nowrap">{{ $collection->translateAttribute('name') }}</a>
                        @else
                            <a href="{{ \App\Services\WebstoreHelperService::getCollectionRoute($collection) }}" class="hover:underline text-nowrap">{{ $collection->translateAttribute('name') }}</a>
                        @endif
                    @endforeach
                </div>
            </div>

            <hr class="mb-4 border-gray-200 dark:border-gray-500">

            <div class="font-light text-black text-base dark:text-white [&>p]:mb-4 last:[&>p]:mb-0">
                {!! __($product->translateAttribute('description')) !!}
            </div>

            <div class="pb-4">
                @php
                    $specifications = $product->attribute_data->except(['name', 'description'])->filter(fn($value, $handle) => $product->translateAttribute($handle));
                    $brandLink = $product->brand?->translateAttribute('link');
                @endphp

                @if($specifications->isNotEmpty() || $product->brand || $brandLink)
                    <div id="hs-show-hide-collapse-heading" class="hs-collapse hidden flex flex-col gap-2 mb-2 w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-show-hide-collapse">
                        @if($product->brand)
                            <hr class="border-gray-200 dark:border-gray-500"/>
                            <div class="grid grid-cols-2 gap-2">
                                <span class="font-medium text-black dark:text-white">{{ ($product->brand->translateAttribute('brand_is_designer') ?? false) ? __('Designer') : __('Brand') }}:</span>
                                <span class="text-end text-black dark:text-white">
                                    @if($brandLink)
                                        <a href="{{ $brandLink }}" target="_blank" class="hover:underline">{{ $product->brand->name }}</a>
                                    @else
                                        {{ $product->brand->name }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @foreach($specifications as $handle => $value)
                            @continue(in_array($handle, ['brand_is_designer', 'link']))
                            <hr class="border-gray-200 dark:border-gray-500"/>
                            <div class="grid grid-cols-2 gap-2">
                                <span class="font-medium text-black dark:text-white">{{ Str::headline($handle) }}:</span>
                                <span class="text-end text-black dark:text-white">{{ $product->translateAttribute($handle) }}</span>
                            </div>
                        @endforeach

                        <hr class="border-gray-200 dark:border-gray-500"/>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="hs-collapse-toggle inline-flex items-center gap-x-1 text-xs font-bold avenir-bold uppercase tracking-widest text-primary-dark decoration-2 hover:text-primary hover:underline focus:outline-hidden focus:underline focus:text-primary disabled:opacity-50 disabled:pointer-events-none dark:text-primary dark:hover:text-primary-light dark:focus:text-primary-light" id="hs-show-hide-collapse" aria-expanded="false" aria-controls="hs-show-hide-collapse-heading" data-hs-collapse="#hs-show-hide-collapse-heading">
                            <span class="hs-collapse-open:hidden">{{ __('Show specifications') }}</span>
                            <span class="hs-collapse-open:block hidden">{{ __('Hide specifications') }}</span>
                            <svg class="hs-collapse-open:rotate-180 shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <div class="flex flex-col space-y-6 mt-auto">
                <div class="grid grid-cols-4 grid-flow-row-dense gap-4">
                    @foreach ($this->productOptions as $option)
                        <span class="pr-2 self-center text-xs font-bold avenir-bold uppercase tracking-widest text-gray-700 dark:text-white border-r border-gray-200 dark:border-gray-500 col">
                            {{ __($option['option']->translate('name')) }}
                        </span>

                        <div class="flex flex-grow flex-wrap flex-row-reverse gap-2 text-xs tracking-wide uppercase col-span-3">
                            @foreach ($option['values'] as $value)
                                @if (array_search($value->id, $selectedOptionValues))
                                    <x-ui.button.default.primary type="button" class="inline !px-4 !py-2" wire:click="setSelectedOptionValue('{{ $option['option']->id }}', {{ $value->id }})" wire:loading.attr="disabled">
                                        {{ __($value->translate('name')) }}
                                    </x-ui.button.default.primary>
                                @else
                                    <x-ui.button.outline.primary type="button" class="inline !px-4 !py-2" wire:click="setSelectedOptionValue('{{ $option['option']->id }}', {{ $value->id }})" wire:loading.attr="disabled">
                                        {{ __($value->translate('name')) }}
                                    </x-ui.button.outline.primary>
                                @endif
                            @endforeach
                        </div>

                        @if (!$loop->last)
                            <hr class="mx-1 col-span-4 border-gray-200 dark:border-gray-500" />
                        @endif
                    @endforeach
                </div>

                <livewire:sytatsu.components.add-to-cart :purchasable="$this->variant" :wire:key="$this->variant->id" />
            </div>
        </div>
    </div>
</div>
