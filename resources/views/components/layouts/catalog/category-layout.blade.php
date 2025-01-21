<x-layouts.catalog.catalog-layout>
    <x-slot name="actions">
        <x-nav-link :href="route('catalog.categories.list')" :active="request()->routeIs('catalog.categories.list')">
            {{ __('List') }}
        </x-nav-link>

        <x-nav-link :href="route('catalog.categories.create')" :active="request()->routeIs('catalog.categories.create')">
            <i class="fa fa-plus mr-1"></i> {{ __('Create') }}
        </x-nav-link>
    </x-slot>

    {{ $slot }}
</x-layouts.catalog.catalog-layout>
