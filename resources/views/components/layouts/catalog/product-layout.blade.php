<x-layouts.catalog.catalog-layout>
    <x-slot name="actions">
        <x-child-nav-link :href="route('catalog.products.list')" :active="request()->routeIs('catalog.products.list')">
            {{ __('List') }}
        </x-child-nav-link>

        <x-child-nav-link :href="route('catalog.products.create')" :active="request()->routeIs('catalog.products.create')">
            <i class="fa fa-plus mr-1"></i> {{ __('Create') }}
        </x-child-nav-link>
    </x-slot>

    {{ $slot }}
</x-layouts.catalog.catalog-layout>
