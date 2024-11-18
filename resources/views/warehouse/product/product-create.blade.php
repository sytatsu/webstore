<x-layouts.warehouse.product-layout>
    <form method="post" action="{{ route('warehouse.products.store') }}" class="space-y-6">
        @csrf
        @method('put')

        @include('warehouse.product.partials.product-fields', [
            'brands' => $brands,
            'categories' => $categories,
        ])
    </form>
</x-layouts.warehouse.product-layout>
