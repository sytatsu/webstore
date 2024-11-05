<x-layouts.warehouse.brand-layout>
    @include('warehouse.brand.partials.brand-from', [
        'method' => 'patch',
        'action' => route('warehouse.brands.update', $brand),
        'brand' => $brand
    ])
</x-layouts.warehouse.brand-layout>
