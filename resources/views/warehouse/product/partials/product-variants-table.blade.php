<div class="flex flex-col gap-4">
    <div class="flex flex-row justify-between">
        <span class='block avenir-bold text-lg text-gray-700 my-auto ml-2'>
            {{ __("Product variants") }}
        </span>

        <x-secondary-button class="my-auto">
            <i class="fa fa-plus pr-1"></i>{{ __("New Variant") }}
        </x-secondary-button>
    </div>

    <div class="flex flex-col">
        <x-table>
            <x-slot name="content">
                @foreach($product->productVariants as $productVariant)
                    <x-table.row>
                        <td class="pl-3">
                            {{ $productVariant->name }}
                        </td>
                        <td>
                            {{ $productVariant->price->formatted() }}
                        </td>
                        <td>
                            {{ $productVariant->variant->name }}
                        </td>

                        <td>
                            {{ $productVariant->availabilityType->translation() }}
                        </td>
                        <td>
                            {{ $productVariant->availabilityType === \App\Enums\AvailabilityEnum::STOCK ? $productVariant->availabilityQuantity : '-' }}
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

                            <x-actions.button>
                                <i class="fa fa-pencil pr-1"></i>{{ __('Edit') }}
                            </x-actions.button>
                        </x-slot>
                    </x-table.row>
                @endforeach
            </x-slot>
        </x-table>
    </div>
</div>
