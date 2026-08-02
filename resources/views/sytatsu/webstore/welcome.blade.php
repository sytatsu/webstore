<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto">
    <x-sytatsu.homepage.clickerz-hero />

    <div id="products" class="flex flex-col @if($showFilters ?? false) md:grid md:grid-cols-4 @endif gap-8">
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
