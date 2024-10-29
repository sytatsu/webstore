<x-layouts.warehouse.warehouse-layout>
    <x-slot name="actions">
        <x-child-nav-link :href="route('warehouse.categories.list')" :active="request()->routeIs('warehouse.categories.list')">
            {{ __('List') }}
        </x-child-nav-link>

        <x-child-nav-link :href="route('warehouse.categories.create')" :active="request()->routeIs('warehouse.categories.create')">
            <i class="fa fa-plus mr-1"></i> {{ __('Create') }}
        </x-child-nav-link>
    </x-slot>

    <x-section width="w-full" class="!p-2">
        @if ($errors->any())
            @dump($errors)
        @endif

        {{ $slot }}

    </x-section>
</x-layouts.warehouse.warehouse-layout>
