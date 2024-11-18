<x-layouts.warehouse.brand-layout>
    <x-section width="w-full" class="!p-2">
        @include('warehouse.brand.partials.brand-form', [
            'method' => 'put',
            'action' => route('warehouse.brands.store'),
        ])
    </x-section>
</x-layouts.warehouse.brand-layout>
