<x-layouts.catalog.product-layout>
    <form method="post" action="{{ route('catalog.products.variants.update', $product) }}" class="space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="uuid" value="{{ $productVariant->uuid }}">

        <x-section width="w-full" innerClass="space-y-6">
            <x-section-header>{{ $productVariant->name }}</x-section-header>

            @include('catalog.product.partials.product-variant-fields', [
               'isUnique' => true,
               'productVariant' => $productVariant,
           ])

        </x-section>

        <x-section width="w-full" class="p-6 space-y-6">
            <div class="flex items-center gap-4">
                <x-primary-button class="flex-grow justify-center">{{ __('Save') }}</x-primary-button>
            </div>
        </x-section>
    </form>
</x-layouts.catalog.product-layout>
