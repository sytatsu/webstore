@props([
    'title',
    'brands',
    'categories',
    'product' => null
])


<x-section width="w-full" innerClass="space-y-6">
    <x-section-header>
        {{ $title }}
    </x-section-header>

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
