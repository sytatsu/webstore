<div class="flex flex-col gap-4">

    <x-section-header>
        {{ __('Details') }}

        <x-slot name="actions">
            <x-secondary-button-link class="my-auto" href="{{ route('warehouse.products.variants.create', $product) }}">
                <i class="fa fa-plus pr-1"></i>{{ __("New Variant") }}
            </x-secondary-button-link>
        </x-slot>
    </x-section-header>

    <div class="flex flex-col">
        <x-table class="!p-0">
            <x-slot name="header">
                <x-table.row-head>
                    <th class="pl-3">{{ __('Variant(s)') }}</th>
                    <th>{{ __('SKU') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>
                        <span>{{ __('Availability') }}</span>
                        <span class="float-end">{{ __('Stock') }}</span>
                    </th>
                    <th></th>
                </x-table.row-head>
            </x-slot>
            <x-slot name="content">
                @foreach($productVariants as $productVariant)
                    <x-table.row>
                        <td class="pl-3">
                            {{ $productVariant->variants->implode('name', ' | ') }}
                        </td>
                        <td>
                            {{ $productVariant->sku }}
                        </td>
                        <td>
                            {{ $productVariant->price->formatted() }}
                        </td>


                        <td class="py-2">
                            @foreach($productVariant->availability as $availability)
                                <x-availability.show :availability="$availability" />

                                @if (!$loop->last)
                                    <hr />
                                @endif
                            @endforeach
                        </td>

                        <x-slot name="actions">
                            @if($productVariant->availabilityType === \App\Enums\AvailabilityEnum::STOCK)
                                <x-actions.button>
                                    <i class="fa fa-box pr-1"></i>{{ __('Add stock') }}
                                </x-actions.button>
                            @endif

                            <x-actions.button>
                                <i class="fa fa-eye pr-1"></i>{{ __('Show') }}
                            </x-actions.button>

                            <x-actions.button href="{{ route('warehouse.products.variants.edit', ['product' => $product, 'productVariant' => $productVariant]) }}">
                                <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                            </x-actions.button>
                        </x-slot>
                    </x-table.row>
                @endforeach
            </x-slot>
        </x-table>
    </div>
</div>
