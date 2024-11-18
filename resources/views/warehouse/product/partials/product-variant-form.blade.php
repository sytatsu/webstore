
<x-input.text name="product_variant.name" :label="__('Name')" :value="$productVariant?->name" class="mt-1 block w-full"/>
<x-input.text name="product_variant.description" :label="__('Description')" :value="$productVariant?->description" class="mt-1 block w-full"/>
<x-input.price name="product_variant.price" :label="__('price')" :value="$productVariant?->price->string()" class="mt-1 block w-full"/>

