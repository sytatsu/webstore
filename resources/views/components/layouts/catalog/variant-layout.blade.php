<x-layouts.catalog.catalog-layout>
    <x-slot name="actions">
        <x-child-nav-link :href="route('catalog.variants.list')" :active="request()->routeIs('catalog.variants.list')">
            {{ __('List') }}
        </x-child-nav-link>

        <x-child-nav-link :href="route('catalog.variants.create')" :active="request()->routeIs('catalog.variants.create')">
            <i class="fa fa-plus mr-1"></i> {{ __('Create') }}
        </x-child-nav-link>
    </x-slot>

    {{ $slot }}
</x-layouts.catalog.catalog-layout>
