<div class="flex flex-col gap-8" x-data x-init="HSCollapse.autoInit()">
    @if($showSorting)
        <div class="flex flex-col gap-6 rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8">

            <button wire:click="toggleSorting" type="button" class="hs-collapse-toggle md:hidden inline-flex items-center w-full justify-between gap-x-1 font-mono text-[10px] tracking-[.16em] uppercase text-primary-dark decoration-2 hover:text-primary hover:underline focus:outline-hidden focus:underline focus:text-primary disabled:opacity-50 disabled:pointer-events-none dark:text-primary dark:hover:text-primary-light dark:focus:text-primary-light {{ $isSortingExpanded ? 'active' : '' }}" id="hs-sorting-collapse" aria-expanded="{{ $isSortingExpanded ? 'true' : 'false' }}" aria-controls="hs-sorting-collapse-content" data-hs-collapse="#hs-sorting-collapse-content">
                <span class="{{ $isSortingExpanded ? 'hidden' : 'block' }}">{{ __('Show sorting') }}</span>
                <span class="{{ $isSortingExpanded ? 'block' : 'hidden' }}">{{ __('Hide sorting') }}</span>
                <svg class="{{ $isSortingExpanded ? 'rotate-180' : '' }} shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <div id="hs-sorting-collapse-content" class="hs-collapse {{ $isSortingExpanded ? 'open' : 'hidden' }} md:block w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-sorting-collapse">
                <div class="flex flex-col gap-6">
                        <div>
                            <h3 class="hidden md:inline-block text-xl font-bold text-black dark:text-white avenir-bold uppercase mb-3">{{ __('Sort By') }}</h3>
                            <x-ui.input.default.select wire:model="selectedSort" wire:change="applyFilters" class="w-full">
                                <option value="newest">{{ __('Newest to Oldest') }}</option>
                                <option value="oldest">{{ __('Oldest to Newest') }}</option>
                                <option value="alphabetical">{{ __('Alphabetically') }}</option>
                            </x-ui.input.default.select>
                        </div>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-6 rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8">
        <div class="flex items-center justify-between md:block">
            <h3 class="hidden md:inline text-lg font-bold text-black dark:text-white avenir-bold uppercase md:mb-4">{{ __('Filters') }}</h3>

            <button wire:click="toggleFilters" type="button" class="hs-collapse-toggle md:hidden inline-flex items-center w-full justify-between gap-x-1 font-mono text-[10px] tracking-[.16em] uppercase text-primary-dark decoration-2 hover:text-primary hover:underline focus:outline-hidden focus:underline focus:text-primary disabled:opacity-50 disabled:pointer-events-none dark:text-primary dark:hover:text-primary-light dark:focus:text-primary-light {{ $isFiltersExpanded ? 'active' : '' }}" id="hs-filters-collapse" aria-expanded="{{ $isFiltersExpanded ? 'true' : 'false' }}" aria-controls="hs-filters-collapse-content" data-hs-collapse="#hs-filters-collapse-content">
                <span class="{{ $isFiltersExpanded ? 'hidden' : 'block' }}">{{ __('Show filters') }}</span>
                <span class="{{ $isFiltersExpanded ? 'block' : 'hidden' }}">{{ __('Hide filters') }}</span>
                <svg class="{{ $isFiltersExpanded ? 'rotate-180' : '' }} shrink-0 size-4 block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>
        </div>

        <div id="hs-filters-collapse-content" class="hs-collapse {{ $isFiltersExpanded ? 'open' : 'hidden' }} md:block w-full overflow-hidden transition-[height] duration-300 md:border-t md:border-gray-200 md:dark:border-gray-500 md:pt-6" aria-labelledby="hs-filters-collapse">
            <div class="divide-y divide-gray-200 dark:divide-gray-500">
                <!-- Category/Sub-collection Filter -->
                @if($showCategories && $subCollections->isNotEmpty())
                    <div class="pb-6 first:pt-0">
                        <h4 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase mb-3">{{ __('Sub-categories') }}</h4>
                        <div class="flex flex-col gap-2">
                            @foreach($subCollections as $subCollection)
                                <x-ui.input.default.checkbox
                                           wire:model="selectedSubCollections"
                                           wire:change="applyFilters"
                                           value="{{ $subCollection->id }}"
                                           :label="$subCollection->translateAttribute('name')" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Price Filter -->
                @if($showPrice)
                    <div class="py-6 first:pt-0">
                        <h4 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase mb-3">{{ __('Price Range') }}</h4>
                        <div class="flex flex-col gap-4">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="min-price" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min</label>
                                    <input type="number" id="min-price"
                                           wire:model.debounce.500ms="minPrice"
                                           wire:change.debounce.600ms="applyFilters"
                                           placeholder="€ 0"
                                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-primary focus:ring-primary disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600">
                                </div>
                                <div>
                                    <label for="max-price" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Max</label>
                                    <input type="number" id="max-price"
                                           wire:model.debounce.500ms="maxPrice"
                                           wire:change.debounce.600ms="applyFilters"
                                           placeholder="€"
                                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-primary focus:ring-primary disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status/Availability Filter -->
                @if($showAvailability)
                    <div class="py-6 first:pt-0">
                        <h4 class="text-sm font-bold text-black dark:text-white avenir-bold uppercase mb-3">{{ __('Availability') }}</h4>
                        <div class="flex flex-col gap-2">
                            <x-ui.input.default.checkbox
                                       wire:model="inStockOnly"
                                       wire:change="applyFilters"
                                       :label="__('In Stock Only')" />
                        </div>
                    </div>
                @endif

                <div class="pt-6 first:pt-0 flex flex-col gap-2">
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
</div>
