 <x-layouts.catalog.product-layout>
    <x-section width="w-full" innerClass="space-y-6 flex-col">
        <x-section-header>
            {{ $product->name }}

            <x-slot name="actions">
                <div class="text-end">
                    <x-secondary-button-link :href="route('catalog.products.edit',  ['product' => $product, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                        <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                    </x-secondary-button-link>

                    @if(is_null($product->discontinuedAt))
                        <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-product-discontinue')">
                            <i class="fa fa-ban pr-1"></i>{{ __('Discontinue') }}
                        </x-secondary-button>
                        @include('catalog.product.partials.product-discontinue-modal', $product)
                    @else
                        <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-product-continue')">
                            <i class="fa fa-check-circle pr-1"></i>{{ __('Continue') }}
                        </x-secondary-button>
                        @include('catalog.product.partials.product-continue-modal', $product)

                        <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-product-deletion')">
                            <i class="fa fa-trash pr-1"></i>{{ __('Delete') }}
                        </x-secondary-button>
                        @include('catalog.product.partials.product-delete-modal', $product)
                    @endif
                </div>
            </x-slot>
        </x-section-header>

        <div class="px-2">
            <x-data-field :title="__('Description')">
                @if ($product->description)
                    {{ $product->description }}
                @else
                    <i class="muted">No description available</i>
                @endif
            </x-data-field>

            <hr class="m-2" />

            <div class="flex flex-row pt-4 flex-wrap">
                <x-data-field :title="__('Product Type')" class="w-1/3">
                    {{ $product->productType->translation() }}
                </x-data-field>

                <x-data-field :title="__('Brand')" class="w-1/3">
                    @if($product->brand)
                        <a class="hover:underline text-md" href="{{ route('catalog.brands.show', $product->brand) }}">{{ $product->brand->name }}</a>
                    @else
                        <span class="text-sm rounded-lg py-1 px-2 my-1 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>{{ __('N/A') }}</span>
                    @endif
                </x-data-field>

                <x-data-field :title="__('Category')" class="w-1/3">
                    @if($product->category)
                        <a class="hover:underline text-md" href="{{ route('catalog.categories.show', $product->category) }}">{{ $product->category->name }}</a>
                    @else
                        <span class="text-sm rounded-lg py-1 px-2 my-1 bg-red-500 text-white"><i class="fa fa-warning pr-1"></i>{{ __('N/A') }}</span>
                    @endif
                </x-data-field>

                <x-data-field :title="__('Created at')" class="w-1/3">
                    {{ $product->createdAt }}
                </x-data-field>

                <x-data-field :title="__('Updated at')" class="w-1/3">
                    {{ $product->updatedAt }}
                </x-data-field>

                <x-data-field :title="__('Discontinued at')" class="w-1/3">
                    {{ $product->discontinuedAt ?? __('N/A') }}
                </x-data-field>
            </div>
        <div>
    </x-section>

    <x-section width="w-full" innerClass="space-y-6-flex-col">
        @if($product->productVariantType === \App\Enums\ProductVariantType::GENERIC)
            @include('catalog.product.partials.product-multiple-variants', [
                'product' => $product,
                'productVariants' => $product->productVariants
            ])
        @else
            @include('catalog.product.partials.product-unique-variant', [
                'product' => $product,
                'productVariant' => $product->productVariants->first()
            ])
        @endif
    </x-section>
</x-layouts.catalog.product-layout>
