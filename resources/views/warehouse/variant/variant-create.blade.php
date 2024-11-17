<x-layouts.warehouse.variant-layout>
    <x-section width="w-full" class="!p-2">
        @include('warehouse.variant.partials.variant-form', [
           'method' => 'put',
           'action' => route('warehouse.variants.store'),
       ])
    </x-section>
</x-layouts.warehouse.variant-layout>
