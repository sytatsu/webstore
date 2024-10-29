<x-app-layout>
    <x-slot name="header">
        <x-layout-header title='Warehouse' :showheader='false'>
            <x-slot name="submenu">
                <div class="hidden sm:flex justify-between gap-4 my-0">
                    <x-nav-link :href="route('warehouse.products.list')" :active="request()->routeIs('warehouse.products.*')">
                        {{ __('Products') }}
                    </x-nav-link>

                    <x-nav-link :href="route('warehouse.brands.list')" :active="request()->routeIs('warehouse.brands.*')">
                        {{ __('Brands') }}
                    </x-nav-link>

                    <x-nav-link :href="route('warehouse.categories.list')" :active="request()->routeIs('warehouse.categories.*')">
                        {{ __('Categories') }}
                    </x-nav-link>

                    <x-nav-link :href="route('warehouse.variants.list')" :active="request()->routeIs('warehouse.variants.*')">
                        {{ __('Variants') }}
                    </x-nav-link>
                </div>
            </x-slot>

            <div class="gap-2">
                @if(isset($actions))
                    {{ $actions }}
                @endif
            </div>
        </x-layout-header>
    </x-slot>

    <x-container>
        {{ $slot }}
    </x-container>
</x-app-layout>
