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
                <div class="flex flex-col gap-8"
                     @if($bundle ?? null)
                         x-data="{ pickModeOn: {{ ($bundleEditLineId ?? null) ? 'true' : 'false' }} }"
                     @endif
                >
                    @if($bundle ?? null)
                        {{-- Additive entry point only — nothing below this changes when it's off. --}}
                        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-4 px-6 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
                            <div class="flex-1">
                                <p class="avenir-bold text-black dark:text-white">
                                    {{ __('Build your own :name & save', ['name' => $bundle->getTranslatedName()]) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Mix and match products from this collection at a price that drops the more you pick.') }}
                                </p>
                            </div>
                            <button type="button"
                                    x-on:click="pickModeOn = !pickModeOn"
                                    class="shrink-0 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary hover:opacity-90 transition-opacity"
                            >
                                <span x-show="!pickModeOn">{{ __('Start building') }}</span>
                                <span x-show="pickModeOn" x-cloak>{{ __('Stop building') }}</span>
                            </button>
                        </div>

                        <div x-show="pickModeOn" x-cloak>
                            <livewire:sytatsu.components.bundle.bundle-builder
                                :bundle="$bundle"
                                :edit-line-id="$bundleEditLineId ?? null"
                                :wire:key="'bundle-builder-'.$bundle->id"
                            />
                        </div>
                    @endif

                    @if($products->isNotEmpty())
                        <div class="rounded-2xl shadow-md dark:shadow-slate-700 bg-white dark:bg-slate-800 py-8 px-6 lg:p-12">
                            <div class="divide-y divide-gray-200 dark:divide-gray-500">
                                <div class="flex flex-row justify-between items-center pb-8">
                                    <span class="text-2xl avenir-bold text-black dark:text-white">
                                        {{ $collection->translateAttribute('name') }}
                                    </span>
                                </div>

                                <div class="pt-8 grid {{ $gridColumns }} gap-x-4 gap-y-6 md:gap-6 lg:gap-8 xl:gap-12">
                                    @foreach($products as $product)
                                        @if(($bundle ?? null) && ($bundleEligibleProductIds ?? collect())->contains($product->id))
                                            <div class="relative" wire:key="tile-wrap-{{ $product->id }}">
                                                <livewire:sytatsu.components.product.product-tile :product="$product" :wire:key="'product-'.$product->id.'-'.md5($product->updated_at)" />

                                                <div class="absolute top-2 right-2" x-show="pickModeOn" x-cloak>
                                                    <livewire:sytatsu.components.bundle.bundle-pick-control
                                                        :product="$product"
                                                        :initial-selection="$bundleEditSelection ?? []"
                                                        :wire:key="'bundle-pick-'.$bundle->id.'-'.$product->id"
                                                    />
                                                </div>
                                            </div>
                                        @else
                                            <livewire:sytatsu.components.product.product-tile :product="$product" :wire:key="'product-'.$product->id.'-'.md5($product->updated_at)" />
                                        @endif
                                    @endforeach
                                </div>
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
