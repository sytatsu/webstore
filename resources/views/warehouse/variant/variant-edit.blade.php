<x-layouts.warehouse.variant-layout>
    <x-section width="w-full" class="!p-2">
        @include('warehouse.variant.partials.variant-form', [
            'method' => 'patch',
            'action' => route('warehouse.variants.update', ['action' => $action]),
            'variant' => $variant
        ])
    </x-section>
</x-layouts.warehouse.variant-layout>
