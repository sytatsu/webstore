<x-layouts.warehouse.category-layout>
    <x-section width="w-full" class="!p-2">
        @include('warehouse.category.partials.category-form', [
           'method' => 'put',
           'action' => route('warehouse.categories.store'),
       ])
    </x-section>
</x-layouts.warehouse.category-layout>
