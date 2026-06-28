<div
    x-data="{ show: @entangle('isOpen') }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-[100] overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"
            @click="show = false"
        ></div>

        {{-- Modal content --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-2xl px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-slate-800 shadow-xl rounded-lg sm:my-8 sm:align-middle sm:p-6"
        >
            <x-ui.spinner-overlay wire:loading.flex wire:target="addToBundle, setSelectedOptionValue" />

            @if ($product)
                <div class="flex flex-col md:grid md:grid-cols-2 gap-8">
                    {{-- Left side: Carousel --}}
                    <div>
                        <livewire:sytatsu.components.product.carousel
                            :product="$product"
                            :images="$variant ? (count($variant->images) ? $variant->images : $product->images) : $product->images"
                            :wire:key="'modal-carousel-'.($variant ? $variant->id : $product->id)"
                            :disableLink="true"
                        />
                    </div>

                    {{-- Right side: Details --}}
                    <div class="flex flex-col gap-4">
                        <div class="flex justify-between items-start">
                            <h2 class="text-xl font-bold text-black dark:text-white avenir-bold uppercase tracking-widest leading-tight">
                                {{ $product->translateAttribute('name') }}
                            </h2>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="text-lg text-black dark:text-white avenir-bold uppercase">
                            {{ $variant ? $variant->prices->first()?->price->formatted() : '' }}
                        </p>

                        <div class="font-light text-black dark:text-white text-sm line-clamp-4">
                            {!! __($product->translateAttribute('description')) !!}
                        </div>

                        <hr class="border-gray-200 dark:border-gray-500">

                        <div class="flex flex-col space-y-4">
                            @if($this->availableStock === 0)
                                <div class="bg-primary/10 border-2 border-primary text-primary px-4 py-2 rounded font-bold avenir-bold uppercase tracking-widest text-center text-xs">
                                    {{ __('Out of stock') }}
                                </div>
                            @endif

                            @foreach ($this->productOptions as $option)
                                <div class="flex flex-col gap-2">
                                    <span class="text-xs font-bold avenir-bold uppercase tracking-widest text-gray-700 dark:text-white">
                                        {{ __($option['option']->translate('name')) }}
                                    </span>
                                    <div class="flex flex-wrap gap-2 text-[10px] tracking-wide uppercase">
                                        @foreach ($option['values'] as $value)
                                            @php
                                                $isSelected = isset($selectedOptionValues[$option['option']->id]) && $selectedOptionValues[$option['option']->id] == $value->id;
                                            @endphp
                                            <button
                                                type="button"
                                                wire:click="setSelectedOptionValue({{ $option['option']->id }}, {{ $value->id }})"
                                                @class([
                                                    'px-3 py-1.5 border-2 transition-all font-bold avenir-bold uppercase tracking-widest',
                                                    'bg-primary border-primary text-white' => $isSelected,
                                                    'border-primary text-primary hover:bg-primary/10' => !$isSelected,
                                                ])
                                            >
                                                {{ __($value->translate('name')) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-auto pt-4 flex items-center gap-4">
                            @if(($this->availableStock ?? 1) > 0)
                                <div class="flex items-center border-2 border-primary">
                                    <button
                                        type="button"
                                        wire:click="decrement"
                                        class="px-4 py-2 text-primary dark:text-white hover:bg-primary hover:text-white transition-colors font-bold"
                                    >
                                        −
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold avenir-bold text-black dark:text-white uppercase tracking-widest">{{ $quantity }}</span>
                                    <button
                                        type="button"
                                        wire:click="increment"
                                        @disabled($this->availableStock !== null && $quantity >= $this->availableStock)
                                        class="px-4 py-2 text-primary dark:text-white hover:bg-primary hover:text-white transition-colors font-bold disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-primary"
                                    >
                                        +
                                    </button>
                                </div>

                                <x-ui.button.default.primary
                                    wire:click="addToBundle"
                                    class="flex-1 !py-2.5"
                                >
                                    {{ __('Add to bundle') }}
                                </x-ui.button.default.primary>
                            @else
                                <x-ui.button.default.primary
                                    @click="show = false"
                                    class="flex-1 !py-2.5"
                                >
                                    {{ __('Close') }}
                                </x-ui.button.default.primary>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
