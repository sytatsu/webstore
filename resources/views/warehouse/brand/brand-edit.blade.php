<x-layouts.warehouse.brand-layout>
    <x-section width="w-full" class="!p-2">
        @include('warehouse.brand.partials.brand-from', [
            'method' => 'patch',
            'action' => route('warehouse.brands.update', ['action' => $action]),
            'brand' => $brand
        ])
    </x-section>
</x-layouts.warehouse.brand-layout>
