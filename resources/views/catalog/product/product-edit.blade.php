<x-layouts.catalog.product-layout>
    <form method="post" action="{{ route('catalog.products.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $product->uuid }}">

        @include('catalog.product.partials.product-fields', [
            'brands' => $brands,
            'categories' => $categories,
            'product' => $product,
            'title' => sprintf('Edit: %s', $product->name),
        ])

        <x-section width="w-full" class="p-6 space-y-6">
            <div class="flex items-center gap-4">
                <x-primary-button class="flex-grow justify-center">{{ __('Save') }}</x-primary-button>
            </div>
        </x-section>
    </form>
</x-layouts.catalog.product-layout>
