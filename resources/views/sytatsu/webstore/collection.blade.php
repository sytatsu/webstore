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

<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto flex flex-col gap-8">
    {{-- The page's own title, not squeezed in above the product grid
         (where it used to sit, competing for attention with the grid
         directly below it — and, once a bundle applies, with the
         tray's own name label too) — and in a card like every other
         section on this page (filters, sort, the grid itself), rather
         than sitting bare on the page background. The breadcrumb reuses
         $breadcrumbItems, already built above for the JSON-LD schema
         but never otherwise rendered for a visitor to actually see.
         Shared with the collections listing, custom print, maintenance
         and contact pages via x-sytatsu.page-header so they all render
         the same header (see docs/bundles/README.md).

         gap-8 here, same as every other section-to-section gap on this
         page — a tighter gap was tried here (shrinking this to gap-4 and
         this card's own py-6 to py-4) to close up what looked like extra
         space above the tray, but that tightened every section's spacing
         page-wide instead of just the one that actually had a problem.
         The real surplus was inside the tray itself — see
         bundle-builder.blade.php's own top-padding note. --}}
    <x-sytatsu.page-header :breadcrumb-items="$breadcrumbItems ?? []" :title="$collection->translateAttribute('name')" />

    @if($bundle ?? null)
        {{-- Spans the full container width — both the filter/sort sidebar
             and the product grid below it — rather than being scoped to
             just the grid column, since it applies to this whole page,
             not only the products currently in view. Sticky to the top,
             under the site header (see bundle-builder.blade.php); this is
             the only way to review/complete/edit the picks, and it's
             always visible, not behind a toggle — see
             docs/bundles/README.md. --}}
        <livewire:sytatsu.components.bundle.bundle-builder
            :bundle="$bundle"
            :edit-line-id="$bundleEditLineId ?? null"
            :wire:key="'bundle-builder-'.$bundle->id"
        />
    @endif

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
                <div class="flex flex-col gap-8">
                    @if($products->isNotEmpty())
                        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
                            <div class="grid {{ $gridColumns }} gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
                                @foreach($products as $product)
                                    <livewire:sytatsu.components.product.product-tile :product="$product" :wire:key="'product-'.$product->id.'-'.md5($product->updated_at)" />
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 p-8 md:p-12 text-center">
                            <p class="text-gray-600 dark:text-gray-400 avenir-bold uppercase">
                                {{ __('No products found') }}
                            </p>
                        </div>
                    @endif

                    @if($products->hasPages())
                        <div class="flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
