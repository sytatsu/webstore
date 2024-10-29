<x-layouts.warehouse.warehouse-layout>
    <x-slot name="actions">
        <x-child-nav-link :href="route('warehouse.variants.list')" :active="request()->routeIs('warehouse.variants.list')">
            {{ __('List') }}
        </x-child-nav-link>

        <x-child-nav-link :href="route('warehouse.variants.create')" :active="request()->routeIs('warehouse.variants.create')">
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
