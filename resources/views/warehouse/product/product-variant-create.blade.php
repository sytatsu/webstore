<x-layouts.warehouse.product-layout>
    <form method="post" action="{{ route('warehouse.products.variants.store', $product) }}" class="space-y-6">
        @csrf
        @method('put')
        <x-section width="w-full" innerClass="space-y-6">
            <x-section-header>New Product Variant</x-section-header>

            @include('warehouse.product.partials.product-variant-fields', [
               'isUnique' => true,
               'productVariant' => null,
           ])

        </x-section>

        <x-section width="w-full" class="p-6 space-y-6">
            <div class="flex items-center gap-4">
                <x-primary-button class="flex-grow justify-center">{{ __('Save') }}</x-primary-button>
            </div>
        </x-section>
    </form>
</x-layouts.warehouse.product-layout>
