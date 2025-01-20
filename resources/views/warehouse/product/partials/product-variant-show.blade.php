@props([
    'extended' => false,
    'productVariant',
])

<div class="flex flex-row pt-4 flex-wrap">
    <x-data-field :title="__('Variant')" class="w-1/3">
        {{ $productVariant->variants->implode('name', ' | ') }}
    </x-data-field>

    <x-data-field :title="__('Sku')" class="w-1/3">
        {{ $productVariant->sku }}
    </x-data-field>

    <x-data-field :title="__('Price')" class="w-1/3">
        {{ $productVariant->price->formatted() }}
    </x-data-field>

    @if ($extended)
        <x-data-field :title="__('Product Type')" class="w-1/3">
            {{ $productVariant->product->productType->translation() }}
        </x-data-field>

        <x-data-field :title="__('Created at')" class="w-1/3">
            {{ $productVariant->createdAt }}
        </x-data-field>

        <x-data-field :title="__('Updated at')" class="w-1/3">
            {{ $productVariant->updatedAt }}
        </x-data-field>
    @endif
</div>

<hr class="m-2" />

<div class="flex flex-row pt-4">
    <x-data-field :title="__('Availability')" class="w-full">
        <x-table class="!p-0">
            <x-slot name="content">
                @foreach($productVariant->availability as $availability)
                    <x-table.row>
                        <td class="pl-3">
                            <x-availability.show :availability="$availability" />
                        </td>

                        <x-slot name="actions">
                            @include('warehouse.product.partials.product-variant-availability-action', ['productVariant' => $productVariant])
                        </x-slot>
                    </x-table.row>
                @endforeach
            </x-slot>
        </x-table>
    </x-data-field>
</div>
