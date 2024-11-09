<x-layouts.warehouse.category-layout>
    @include('warehouse.category.partials.category-form', [
        'method' => 'patch',
        'action' => route('warehouse.categories.update', ['action' => $action]),
        'category' => $category
    ])
</x-layouts.warehouse.category-layout>
