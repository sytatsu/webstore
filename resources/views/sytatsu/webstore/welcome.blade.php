<div class="{{ $maxWidth ?? 'max-w-[85rem]' }} w-full mx-auto">
    @if(\App\Filament\Pages\HomepageHeroSettingsPage::current() === 'clickerz')
        <x-sytatsu.homepage.clickerz-hero />
    @elseif(\App\Filament\Pages\HomepageHeroSettingsPage::current() === 'mini-friends')
        <x-sytatsu.homepage.mini-friends-hero />
    @else
        <x-sytatsu.homepage.main-hero />
    @endif

    <div id="products" class="flex flex-col @if($showFilters ?? false) md:grid md:grid-cols-4 @endif gap-8">
        <!-- Filter Section -->
        @if($showFilters ?? false)
            <div class="md:col-span-1">
                <livewire:sytatsu.components.collection.collection-filters />
            </div>
        @endif

        <!-- Product Grid Section -->
        <div class="@if($showFilters ?? false) md:col-span-3 @endif">
            @if(isset($homepageElements))
                {{-- Homepage Elements (HomeFeaturedCollectionsSettingsPage) is
                     an admin-ordered mix of featured collections and the
                     Clickerz Bar CTA — one Livewire collection-cards
                     instance per collection row (it already accepts a
                     single ProductCollectionDTO) so the CTA can sit
                     between any two of them, rather than always
                     rendering in one fixed spot above the whole list.
                     The `gap-8` here replaces the one collection-cards.blade.php
                     applied internally when a single instance rendered
                     every row itself. --}}
                <div class="flex flex-col gap-8">
                    @foreach($homepageElements as $element)
                        @if($element['type'] === 'clickerz')
                            <x-sytatsu.homepage.clickerz-cta />
                        @else
                            <livewire:sytatsu.components.collection.collection-cards
                                :collections="$element['dto']"
                                :max-width="$maxWidth ?? 'max-w-[85rem]'"
                                :grid-columns="$gridColumns"
                                :key="'home-collection-' . $element['dto']->collection->id"
                            />
                        @endif
                    @endforeach
                </div>
            @elseif(isset($collections) && $collections->isNotEmpty())
                <livewire:sytatsu.components.collection.collection-cards :collections="$collections" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" />
            @elseif(isset($collection) && isset($products))
                <livewire:sytatsu.components.collection.collection-cards :collections="new \App\DTOs\ProductCollectionDTO($collection, $products)" :show-more="false" :max-width="$maxWidth ?? 'max-w-[85rem]'" :grid-columns="$gridColumns" />
            @endif
        </div>
    </div>
</div>
