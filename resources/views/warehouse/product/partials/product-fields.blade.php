@props([
    'brands',
    'categories',
    'product' => null
])


<x-section width="w-full" class="p-6" innerClass="space-y-6">
    <div class="flex flex-row justify-between p-1 bg-slate-700 text-white rounded-lg align-middle">
        <div class="p-4 my-auto">
            <span class='text-2xl avenir-bold'>New Product</span>
        </div>
    </div>

    <x-form.field.text name="name"
                  :label="__('Product name')"
                  :value="$product?->name"
                  class="mt-1 block w-full"/>

    <x-form.field.textarea name="description"
                      :label="__('Description')"
                      :value="$product?->description"
                      class="mt-1 block w-full"/>

    <x-form.field.select name="brand"
                    :label="__('Brand')"
                    :options="$brands"
                    :selected="$product?->brand_uuid"
                    :placeholder="__('Please select a Brand...')"
                    class="mt-1 block w-full"/>

    <x-form.field.select name="category"
                    :label="__('Category')"
                    :options="$categories"
                    :selected="$product?->category_uuid"
                    :placeholder="__('Please select a Category...')"
                    class="mt-1 block w-full"/>

    <x-form.field.select name="product_type"
                    :label="__('Product Type')"
                    :options="\App\Enums\ProductTypeEnum::list()"
                    :selected="$product?->product_type"
                    :placeholder="__('Please select a Product Type...')"
                    class="mt-1 block w-full"/>

    <x-form.field.select name="product_variant_type"
                    :label="__('Product Variant Type')"
                    :options="\App\Enums\ProductVariantType::list()"
                    :selected="$product?->product_variant_type"
                    :placeholder="__('Please select a Product Variant Type...')"
                    class="mt-1 block w-full"/>

</x-section>

@if($product === null  || $product->productVariantType === \App\Enums\ProductVariantType::UNIQUE)
    <x-section width="w-full" class="p-6 space-y-6" innerClass="space-y-6">
        <div class="flex flex-row justify-between p-1 bg-slate-700 text-white rounded-lg align-middle">
            <div class="p-4 my-auto">
                <span class='text-2xl avenir-bold'>Details</span>
            </div>
        </div>

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
