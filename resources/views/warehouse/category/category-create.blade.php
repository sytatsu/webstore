<x-layouts.warehouse.category-layout>
    <form method="post" action="{{ route('warehouse.categories.store') }}" class="p-6 space-y-6">
        @csrf
        @method('put')

        @include('warehouse.category.partials.category-fields', [
           'method' => 'put',
           'action' => route('warehouse.categories.store'),
           'title' => __('New Category')
       ])
    </form>
</x-layouts.warehouse.category-layout>
