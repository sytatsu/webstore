<x-layouts.catalog.category-layout>
    <form method="post" action="{{ route('catalog.categories.store') }}" class="p-6 space-y-6">
        @csrf
        @method('put')

        @include('catalog.category.partials.category-fields', [
           'method' => 'put',
           'action' => route('catalog.categories.store'),
           'title' => __('New Category')
       ])
    </form>
</x-layouts.catalog.category-layout>
