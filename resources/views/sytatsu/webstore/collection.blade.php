<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col @if($showFilters ?? false) md:grid md:grid-cols-6 xl:grid-cols-4 @endif gap-8">
        <!-- Filter Section -->
        @if($showFilters ?? false)
            <div class="md:col-span-2 xl:col-span-1">
                <livewire:sytatsu.components.collection.collection-filters
                    :collection="$collection"
                    :initial-filters="$filters"
                    :show-categories="$showFilterCategories ?? false"
                    :show-price="$showFilterPrice ?? false"
                    :show-availability="$showFilterAvailability ?? false"
                    :show-sorting="$showSorting ?? false"
                />
            </div>
        @endif

        <!-- Product Grid Section -->
        <div class="@if($showFilters ?? false) md:col-span-4 xl:col-span-3 @endif relative">
            <div
                wire:loading.flex
                style="display: none;"
                class="absolute inset-0 z-50 flex items-center justify-center bg-white/50 dark:bg-slate-800/50"
            >
                <svg class="w-12 h-12 text-primary animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            @if(isset($collections) && $collections->isNotEmpty())
                <livewire:sytatsu.components.collection.collection-cards :collections="$collections" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" :wire:key="'collection-cards-'.count($collections)" />
            @elseif(isset($collection) && isset($products))
                <livewire:sytatsu.components.collection.collection-cards :collections="new \App\DTOs\ProductCollectionDTO($collection, $products)" :show-more="false" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" :wire:key="'collection-cards-'.$collection->id.'-'.count($products).'-'.md5(json_encode($filters))" />
            @endif
        </div>
    </div>
</div>
