<x-layouts.warehouse.product-layout>
    <div class="p-6 space-y-6 flex flex-col">
        <div class="flex flex-row justify-between p-1 bg-slate-700 text-white rounded-lg align-middle">
            <div class="p-4 my-auto">
            <span class='text-2xl avenir-bold'>
                {{ $product->name }}
            </span>
            </div>

            <div class="text-end p-4">
                <x-secondary-button-link :href="route('warehouse.products.edit', ['product' => $product, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                    <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                </x-secondary-button-link>

                <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-product-deletion')">
                    <i class="fa fa-trash pr-1"></i>{{ __('Delete') }}
                </x-secondary-button>
            </div>
        </div>

        <div class="flex flex-col p-4 rounded-lg bg-slate-100 shadow">
            <span class='block font-medium text-sm text-gray-700'>{{ __("Description") }}</span>
            <span class='text-md'>{{ $product->description }}</span>
        </div>

        <div class="flex flex-row gap-8 py-1">
            <div class="p-4 rounded-lg bg-slate-100 shadow grow">

                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Product type") }}</span>
                    <span class='text-md'>{{ $product->productType->translation() }}</span>
                </div>

                <hr class="my-1"/>

                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Brand") }}</span>
                    @if($product->brand)
                        <a class="hover:underline" href="{{ route('warehouse.brands.show', $product->brand) }}">{{ $product->brand->name }}</a>
                    @else
                        <span class="text-sm rounded-lg py-1 px-2 my-1 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>N/A</span>
                    @endif
                </div>

                <hr class="my-1"/>

                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Category") }}</span>
                    @if($product->category)
                        <a class="hover:underline" href="{{ route('warehouse.categories.show', $product->category) }}">{{ $product->category->name }}</a>
                    @else
                        <span class="text-sm rounded-lg py-1 px-2 my-1 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>N/A</span>
                    @endif
                </div>
            </div>

            <div class="text-end self-end p-4 rounded-lg bg-slate-100 shadow grow">
                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Created at") }}</span>
                    <span class='text-md'>{{ $product->createdAt }}</span>
                </div>

                <hr class="my-1"/>

                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Updated at") }}</span>
                    <span class='text-md'>{{ $product->updatedAt }}</span>
                </div>

                <hr class="my-1"/>

                <div>
                    <span class='block font-medium text-sm text-gray-700'>{{ __("Discontinued at") }}</span>
                    <span class='text-md'>{{ $product->discontinuedAt ?? 'N/A'}}</span>
                </div>
            </div>
        </div>


        <div class="p-4 rounded-lg bg-slate-100 shadow">
            @if($product->productVariantType === \App\Enums\ProductVariantType::GENERIC)
                @include('warehouse.product.partials.product-variants-table', $product)
            @else
                // Not yet implemented
            @endif
        </div>
    </div>

    @include('warehouse.product.partials.product-delete-modal', $product)
</x-layouts.warehouse.product-layout>
