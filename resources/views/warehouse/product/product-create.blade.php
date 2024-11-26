@props(['product' => null])

<x-layouts.warehouse.product-layout>
    <form method="post" action="{{ route('warehouse.products.store') }}" class="space-y-6">
        @csrf
        @method('put')

        @include('warehouse.product.partials.product-fields', [
            'brands' => $brands,
            'categories' => $categories,
            'title' => "New Product"
        ])


        @if($product === null  || $product->productVariantType === \App\Enums\ProductVariantType::UNIQUE)
            <x-section width="w-full" class="p-6 space-y-6" innerClass="space-y-6">
                <x-section-header>
                    {{ __('Details') }}
                </x-section-header>

                @include('warehouse.product.partials.product-variant-fields', [
                    'productVariant' => $product?->productVariant()->first(),
                ])
            </x-section>
        @endif

        <x-section width="w-full" class="p-6 space-y-6">
            <div class="flex items-center gap-4">
                <x-primary-button class="flex-grow justify-center">{{ __('Save') }}</x-primary-button>
            </div>
        </x-section>
    </form>
</x-layouts.warehouse.product-layout>
