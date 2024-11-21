<x-layouts.warehouse.product-layout>
    <x-section width="w-full" innerClass="space-y-6 flex-col">
        <x-section-header>
            {{ $product->name }}

            <x-slot name="actions">
                <div class="text-end">
                    <x-secondary-button-link :href="route('warehouse.products.edit',  ['product' => $product, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                        <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                    </x-secondary-button-link>

                    <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-product-deletion')">
                        <i class="fa fa-trash pr-1"></i>{{ __('Delete') }}
                    </x-secondary-button>
                </div>
            </x-slot>
        </x-section-header>

        <div class="px-2">
            <div class="flex flex-col pb-4">
                <span class='block font-medium text-xs text-gray-500'>{{ __("Description") }}</span>
                <div class="w-8 border-b my-1 border-gray-200"></div>
                <span class='text-md'>{{ $product->description ?? 'N/A' }}</span>
            </div>

            <hr class="m-2" />

            <div class="flex flex-row pt-4 flex-wrap">
                <div class="flex flex-col pb-4 w-1/3">
                    <span class='font-medium text-xs text-gray-500'>{{ __("Product type") }}</span>
                    <div class="w-8 border-b my-1 border-gray-200"></div>
                    <span class='text-md'>{{ $product->productType->translation() }}</span>
                </div>

                <div class="flex flex-col pb-4 w-1/3">
                    <span class='font-medium text-xs text-gray-500'>{{ __("Brand") }}</span>
                    <div class="w-8 border-b my-1 border-gray-200"></div>
                    @if($product->brand)
                        <a class="hover:underline text-md" href="{{ route('warehouse.brands.show', $product->brand) }}">{{ $product->brand->name }}</a>
                    @else
                        <span class="text-sm rounded-lg py-1 px-2 my-1 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>N/A</span>
                    @endif
                </div>

                <div class="flex flex-col pb-4 w-1/3">
                    <span class='font-medium text-xs text-gray-500'>{{ __("Category") }}</span>
                    <div class="w-8 border-b my-1 border-gray-200"></div>
                    @if($product->category)
                        <a class="hover:underline text-md" href="{{ route('warehouse.categories.show', $product->category) }}">{{ $product->category->name }}</a>
                    @else
                        <span class="text-sm rounded-lg py-1 px-2 my-1 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>N/A</span>
                    @endif
                </div>

                <div class="flex flex-col pb-4 w-1/3">
                    <span class='font-medium text-xs text-gray-500'>{{ __("Created at") }}</span>
                    <div class="w-8 border-b my-1 border-gray-200"></div>
                    <span class='text-md'>{{ $product->createdAt }}</span>
                </div>

                <div class="flex flex-col pb-4 w-1/3">
                    <span class='font-medium text-xs text-gray-500'>{{ __("Updated at") }}</span>
                    <div class="w-8 border-b my-1 border-gray-200"></div>
                    <span class='text-md'>{{ $product->updatedAt }}</span>
                </div>

                <div class="flex flex-col pb-4 w-1/3">
                    <span class='font-medium text-xs text-gray-500'>{{ __("Discontinued at") }}</span>
                    <div class="w-8 border-b my-1 border-gray-200"></div>
                    <span class='text-md'>{{ $product->discontinuedAt ?? 'N/A'}}</span>
                </div>
            </div>
        <div>
    </x-section>

    <x-section width="w-full" class="!p-2">
        <div class="p-6">
            @if($product->productVariantType === \App\Enums\ProductVariantType::GENERIC)
                @include('warehouse.product.partials.product-multiple-variants', [
                    'product' => $product,
                    'productVariants' => $product->productVariants
                ])
            @else
                @include('warehouse.product.partials.product-unique-variant', [
                    'product' => $product,
                    'productVariant' => $product->productVariants->first()
                ])
            @endif
        </div>
    </x-section>

    @include('warehouse.product.partials.product-delete-modal', $product)
</x-layouts.warehouse.product-layout>
