<x-layouts.catalog.catalog-layout>
    <x-slot name="actions">
        <x-child-nav-link :href="route('catalog.categories.list')" :active="request()->routeIs('catalog.categories.list')">
            {{ __('List') }}
        </x-child-nav-link>

        <x-child-nav-link :href="route('catalog.categories.create')" :active="request()->routeIs('catalog.categories.create')">
            <i class="fa fa-plus mr-1"></i> {{ __('Create') }}
        </x-child-nav-link>
    </x-slot>

    {{ $slot }}
</x-layouts.catalog.catalog-layout>
