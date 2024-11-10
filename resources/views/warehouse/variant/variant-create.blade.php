<x-layouts.warehouse.variant-layout>
    @include('warehouse.variant.partials.variant-form', [
       'method' => 'put',
       'action' => route('warehouse.variants.store'),
   ])
</x-layouts.warehouse.variant-layout>
