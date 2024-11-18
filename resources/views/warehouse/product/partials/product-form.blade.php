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

    <div>
        <x-input-label for="name" :value="__('Product name')" />
        <x-text-input :value="old('name') ?? $product?->name ?? ''" placeholde id="name" name="name" type="text" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <x-text-input :value="old('description') ?? $product?->description ?? ''" id="description" name="description" type="text" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="brand" :value="__('Brand')" />
        <x-select-input :options="$brands" :selected="old('brand') ?? $product?->brand_uuid" id="brand" name="brand" placeholder="Please select Brand..." class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('brand')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category" :value="__('Category')" />
        <x-select-input :options="$categories" :selected="old('category') ?? $product?->category_uuid" id="category" name="category" placeholder="Please select Category..." class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('category')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="product_type" :value="__('Product Type')" />
        <x-select-input :options="\App\Enums\ProductTypeEnum::list()" :selected="old('product_type') ?? $product?->product_type" id="product_type" name="product_type" placeholder="Please select Type" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('product_type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="product_variant_type" :value="__('Product Variant Type')" />
        <x-select-input :options="\App\Enums\ProductVariantType::list()" :selected="old('product_variant_type') ?? $product?->product_variant_type" id="product_variant_type" name="product_variant_type" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('product_variant_type')" class="mt-2" />
    </div>
</x-section>

@if($product === null  || $product->productVariantType === \App\Enums\ProductVariantType::UNIQUE)
    <x-section width="w-full" class="p-6 space-y-6" innerClass="space-y-6">
        <div class="flex flex-row justify-between p-1 bg-slate-700 text-white rounded-lg align-middle">
            <div class="p-4 my-auto">
                <span class='text-2xl avenir-bold'>Details</span>
            </div>
        </div>

        @include('warehouse.product.partials.product-variant-form', [
            'productVariant' => $product?->productVariant()->first(),
        ])
    </x-section>
@endif

<x-section width="w-full" class="p-6 space-y-6">
    <div class="flex items-center gap-4">
        <x-primary-button class="flex-grow justify-center">{{ __('Save') }}</x-primary-button>
    </div>
</x-section>
