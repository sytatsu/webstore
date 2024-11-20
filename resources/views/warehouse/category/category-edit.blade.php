<x-layouts.warehouse.category-layout>
    <form method="post" action="{{ route('warehouse.categories.update', ['action' => $action]) }}" class="p-6 space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $category->uuid }}">

        @include('warehouse.category.partials.category-fields', [
            'category' => $category,
            'title' => sprintf('Edit: %s', $category->name),
        ])
    </form>
</x-layouts.warehouse.category-layout>
