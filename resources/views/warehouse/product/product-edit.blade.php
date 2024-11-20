<x-layouts.warehouse.product-layout>
    <form method="post" action="{{ route('warehouse.products.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $product->uuid }}">

        @include('warehouse.product.partials.product-form', [
            'brands' => $brands,
            'categories' => $categories,
            'product' => $product,
            'title' => sprintf('Edit: %s', $product->name),
        ])
    </form>
</x-layouts.warehouse.product-layout>
