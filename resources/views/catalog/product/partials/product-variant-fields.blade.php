@if ($isUnique)
    <x-form.field.text name="product_variant_name"
                       :label="__('Name')"
                       :value="$productVariant?->name"
                       class="mt-1 block w-full"/>

    <x-form.field.textarea name="product_variant_description"
                           :label="__('Description')"
                           :value="$productVariant?->description"
                           class="mt-1 block w-full"/>
@endif

<x-form.field.price name="product_variant_price"
                    :label="__('Price')"
                    :value="$productVariant?->price->string()"
                    class="block w-full"/>

<x-form.field.text name="product_variant_sku"
                   :label="__('Sku')"
                   :value="$productVariant?->sku"
                   class="block w-full"/>

<x-form.field.select name="product_variant_variants"
                     :label="__('Variants')"
                     :options="app(\App\Services\Catalog\VariantService::class)->getVariantList()"
                     :selected="$productVariant?->variants()->pluck('uuid')"
                     :multiple="true"
                     position="top"
                     class="mt-1 block w-full"/>

@if(!isset($productVariant))
    <div class="flex flex-row gap-6 justify-evenly">
        <x-form.field.select name="product_variant_channel_type"
                             :label="__('Channel Type')"
                             :options="\App\Enums\ChannelEnum::list()"
                             selected="huehuehue"
                             class="mt-1 block w-full" outerClass="flex-grow"/>

        <x-form.field.select name="product_variant_channel_location"
                             :label="__('Channel Location')"
                             :options="app(\App\Services\Catalog\ChannelService::class)->getLocationList()"
                             selected="huehuehue"
                             class="mt-1 block w-full" outerClass="flex-grow"/>

        <x-form.field.number name="product_variant_channel_quantity"
                             :label="__('Channel Quantity')"
                             value="0"
                             class="mt-1 block w-full" outerClass="flex-grow"/>
    </div>
@endif

