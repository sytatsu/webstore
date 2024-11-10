<x-layouts.warehouse.variant-layout>
    @include('warehouse.variant.partials.variant-form', [
        'method' => 'patch',
        'action' => route('warehouse.variants.update', ['action' => $action]),
        'variant' => $variant
    ])
</x-layouts.warehouse.variant-layout>
