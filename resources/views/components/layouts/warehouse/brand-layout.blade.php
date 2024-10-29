<x-layouts.warehouse.warehouse-layout>
    <x-slot name="actions">
        <x-child-nav-link :href="route('warehouse.brands.list')" :active="request()->routeIs('warehouse.brands.list')">
            {{ __('List') }}
        </x-child-nav-link>

        <x-child-nav-link :href="route('warehouse.brands.create')" :active="request()->routeIs('warehouse.brands.create')">
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
