<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($homeTitle || $homeSubTitle)
        <div class="mb-12 text-center">
            @if($homeTitle)
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white avenir-bold uppercase mb-4">{{ $homeTitle }}</h1>
            @endif
            @if($homeSubTitle)
                <p class="text-lg text-gray-600 dark:text-gray-400">{{ $homeSubTitle }}</p>
            @endif
        </div>
    @endif
    <div class="flex flex-col @if($showFilters ?? false) md:grid md:grid-cols-4 @endif gap-8">
        <!-- Filter Section -->
        @if($showFilters ?? false)
            <div class="md:col-span-1">
                <livewire:sytatsu.components.collection.collection-filters />
            </div>
        @endif

        <!-- Product Grid Section -->
        <div class="@if($showFilters ?? false) md:col-span-3 @endif">
            @if(isset($collections) && $collections->isNotEmpty())
                <livewire:sytatsu.components.collection.collection-cards :collections="$collections" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" />
            @elseif(isset($collection) && isset($products))
                <livewire:sytatsu.components.collection.collection-cards :collections="new \App\DTOs\ProductCollectionDTO($collection, $products)" :show-more="false" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" />
            @endif
        </div>
    </div>
</div>
