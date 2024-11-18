<x-layouts.warehouse.product-layout>
    <form method="post" action="{{ route('warehouse.products.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $product->uuid }}">

        @include('warehouse.product.partials.product-form', [
            'brands' => $brands,
            'categories' => $categories,
            'product' => $product,
        ])
    </form>
</x-layouts.warehouse.product-layout>
