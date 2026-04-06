@if($this->hasContent)
<div class="flex flex-col gap-6 shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8">
    <div class="flex items-center justify-between md:block">
        <h3 class="text-lg font-bold text-black dark:text-white avenir-bold uppercase md:mb-4">{{ __('Filters') }}</h3>

        <button type="button" class="hs-collapse-toggle md:hidden inline-flex items-center gap-x-1 text-xs font-bold avenir-bold uppercase tracking-widest text-primary-dark decoration-2 hover:text-primary hover:underline focus:outline-hidden focus:underline focus:text-primary disabled:opacity-50 disabled:pointer-events-none dark:text-primary dark:hover:text-primary-light dark:focus:text-primary-light" id="hs-filters-collapse" aria-expanded="false" aria-controls="hs-filters-collapse-content" data-hs-collapse="#hs-filters-collapse-content">
            <span class="hs-collapse-open:hidden">{{ __('Show filters') }}</span>
            <span class="hs-collapse-open:block hidden">{{ __('Hide filters') }}</span>
            <svg class="hs-collapse-open:rotate-180 shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
    </div>

    <hr class="hidden md:block border-gray-200 dark:border-gray-500 mb-6">

    <div id="hs-filters-collapse-content" class="hs-collapse hidden md:block w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-filters-collapse">
        <div class="flex flex-col gap-6">
            <!-- Category/Sub-collection Filter -->
            @if($showCategories && $subCollections->isNotEmpty())
                <div>
                    <h4 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase mb-3">{{ __('Sub-categories') }}</h4>
                    <div class="flex flex-col gap-2">
                        @foreach($subCollections as $subCollection)
                            <x-ui.input.default.checkbox
                                       wire:model="selectedSubCollections"
                                       value="{{ $subCollection->id }}"
                                       :label="$subCollection->translateAttribute('name')" />
                        @endforeach
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-500">
            @endif

            <!-- Price Filter -->
            @if($showPrice)
                <div>
                    <h4 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase mb-3">{{ __('Price Range') }}</h4>
                    <div class="flex flex-col gap-4">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="min-price" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min</label>
                                <input type="number" id="min-price"
                                       wire:model.debounce.500ms="minPrice"
                                       placeholder="€ 0"
                                       class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-primary focus:ring-primary disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600">
                            </div>
                            <div>
                                <label for="max-price" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Max</label>
                                <input type="number" id="max-price"
                                       wire:model.debounce.500ms="maxPrice"
                                       placeholder="€"
                                       class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-primary focus:ring-primary disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-500">
            @endif

            <!-- Status/Availability Filter -->
            @if($showAvailability)
                <div>
                    <h4 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase mb-3">{{ __('Availability') }}</h4>
                    <div class="flex flex-col gap-2">
                        <x-ui.input.default.checkbox
                                   wire:model="inStockOnly"
                                   :label="__('In Stock Only')" />
                    </div>
                </div>
            @endif

            <div class="mt-4 flex flex-col gap-2">
                <x-ui.button.default.primary class="w-full justify-center" wire:click="applyFilters" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="applyFilters">{{ __('Apply Filters') }}</span>
                    <span wire:loading wire:target="applyFilters">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </x-ui.button.default.primary>

                @if($this->hasFilters)
                    <button type="button"
                            wire:click="$parent.resetFilters"
                            class="text-xs text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary transition-colors avenir-bold uppercase">
                        {{ __('Reset Filters') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
