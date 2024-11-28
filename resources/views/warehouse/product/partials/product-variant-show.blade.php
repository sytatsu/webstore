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
    </x-data-field>
</div>
