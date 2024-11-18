<x-field.text name="product_variant_name"
              :label="__('Name')"
              :value="$productVariant?->name"
              class="mt-1 block w-full"/>

<x-field.textarea name="product_variant_description"
                  :label="__('Description')"
                  :value="$productVariant?->description"
                  class="mt-1 block w-full"/>

<x-field.price name="product_variant_price"
               :label="__('Price')"
               :value="$productVariant?->price->string()"
               class="block w-full"/>

<x-field.text name="product_variant_sku"
              :label="__('Sku')"
              :value="$productVariant?->sku"
              class="block w-full"/>

<x-field.select name="product_variant_variants"
                :label="__('Variants')"
                :options="app(\App\Services\Warehouse\VariantService::class)->getVariantList()"
                :selected="$productVariant?->variants()->pluck('uuid')"
                :multiple="true"
                class="mt-1 block w-full"/>


<x-field.select name="product_variant_availability_type"
                :label="__('Availability Type')"
                :options="\App\Enums\AvailabilityEnum::list()"
                selected="huehuehue"
                class="mt-1 block w-full"/>

<x-field.select name="product_variant_availability_location"
                :label="__('Availability Location')"
                :options="app(\App\Services\Warehouse\AvailabilityService::class)->getLocationList()"
                selected="huehuehue"
                class="mt-1 block w-full"/>
