<x-layouts.warehouse.category-layout>
    @include('warehouse.category.partials.category-form', [
       'method' => 'put',
       'action' => route('warehouse.categories.store'),
   ])
</x-layouts.warehouse.category-layout>
