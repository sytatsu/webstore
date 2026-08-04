@if(isset($collection))
    @push('head')
        @php
            $breadcrumbItems = [
                ['name' => __('Homepage'), 'item' => route('sytatsu.webstore.welcome')],
            ];

            if ($collection->parent) {
                $breadcrumbItems[] = [
                    'name' => $collection->parent->translateAttribute('name'),
                    'item' => \App\Services\WebstoreHelperService::getCollectionRoute($collection->parent),
                ];
            }

            $breadcrumbItems[] = ['name' => $collection->translateAttribute('name'), 'item' => url()->current()];

            $breadcrumbSchema = [
                '@' . 'context' => 'https://schema.org',
                '@' . 'type' => 'BreadcrumbList',
                'itemListElement' => collect($breadcrumbItems)->values()->map(fn ($crumb, $index) => [
                    '@' . 'type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['item'],
                ])->all(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush
@endif

<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto">
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
            <x-ui.spinner-overlay wire:loading.flex />
            @if(isset($collections) && $collections->isNotEmpty())
                <livewire:sytatsu.components.collection.collection-cards :collections="$collections" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" :wire:key="'collection-cards-'.count($collections)" />
            @elseif(isset($collection) && isset($products))
                <livewire:sytatsu.components.collection.collection-cards :collections="new \App\DTOs\ProductCollectionDTO($collection, $products)" :show-more="false" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" :wire:key="'collection-cards-'.$collection->id.'-'.count($products).'-'.md5(json_encode($filters))" />
            @endif
        </div>
    </div>
</div>
