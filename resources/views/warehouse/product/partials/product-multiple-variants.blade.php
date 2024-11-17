<div class="flex flex-col gap-4">
    <div class="flex flex-row justify-between bg-slate-700 text-white rounded-lg p-4">
        <span class='block avenir-bold text-lg my-auto ml-2'>
            {{ __("Product variants") }}
        </span>

        <x-secondary-button class="my-auto">
            <i class="fa fa-plus pr-1"></i>{{ __("New Variant") }}
        </x-secondary-button>
    </div>

    <div class="flex flex-col">
        <x-table>
            <x-slot name="header">
                <x-table.row-head class="!bg-gray-200 !text-slate-900 border-b border-gray-400">
                    <th class="pl-3">{{ __('Variant(s)') }}</th>
                    <th>{{ __('SKU') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>
                        <span>{{ __('Availability') }}</span>
                        <span class="float-end">{{ __('Stock') }}</span>
                    </th>
                    <th class="text-end pr-3" >{{ __('Actions')  }}</th>
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
                                <div class="py-1">
                                    <div class="inline">
                                        <span class="avenir-bold">{{ $availability->availabilityType->translation() }}</span>

                                        @if($availability->availabilityType === \App\Enums\AvailabilityEnum::STOCK)
                                            <span>@ {{ $availability->availabilityLocation->label }}</span>
                                        @endif
                                    </div>

                                    @if($availability->availabilityType === \App\Enums\AvailabilityEnum::STOCK)
                                        <span class="float-end">{{ $availability->availabilityQuantity }}</span>
                                    @endif
                                </div>

                                @if (!$loop->last)
                                    <hr/>
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
