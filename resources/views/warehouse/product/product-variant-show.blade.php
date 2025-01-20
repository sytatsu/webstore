 <x-layouts.warehouse.product-layout>
    <x-section width="w-full" innerClass="space-y-6 flex-col">
        <x-section-header>
            {{ $product->name }} > {{ $productVariant->name }}

            <x-slot name="actions">
                <div class="text-end">
                    <x-secondary-button-link :href="route('warehouse.products.variants.edit',  ['product' => $product, 'productVariant' => $productVariant, 'action' => \App\Enums\Actions\SaveAndAction::TO_MODEL->toString()])">
                        <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                    </x-secondary-button-link>

                    <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-product-variant-deletion')">
                        <i class="fa fa-trash pr-1"></i>{{ __('Delete') }}
                    </x-secondary-button>
                    @include('warehouse.product.partials.product-variant-delete-modal', ['product' => $product, 'productVariant' => $productVariant])
                </div>
            </x-slot>
        </x-section-header>

        @include('warehouse.product.partials.product-variant-show', ['productVariant' => $productVariant, 'extended' => true])
    </x-section>
</x-layouts.warehouse.product-layout>
