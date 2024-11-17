<x-layouts.warehouse.category-layout>
    <x-section width="w-full" class="!p-2">
        @include('warehouse.category.partials.category-form', [
            'method' => 'patch',
            'action' => route('warehouse.categories.update', ['action' => $action]),
            'category' => $category
        ])
    </x-section>
</x-layouts.warehouse.category-layout>
