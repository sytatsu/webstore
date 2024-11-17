<div class="flex flex-row justify-between gap-8">
    <div class="p-4 rounded-lg bg-slate-100 shadow grow">
        <div>
            <span class='block font-medium text-sm text-gray-700'>{{ __("Price") }}</span>
            <span class='text-md'>{{ $productVariant->price->formatted() }}</span>
        </div>

        <hr class="my-1"/>

        <div>
            <span class='block font-medium text-sm text-gray-700'>{{ __("Sku") }}</span>
            <span class='text-md'>{{ $productVariant->sku }}</span>
        </div>

        <hr class="my-1"/>

        <div>
            <span class='block font-medium text-sm text-gray-700'>{{ __("Variant") }}</span>
            <span class='text-md'>{{ $productVariant->variants->implode('name', ' | ') }}</span>
        </div>
    </div>

    <div class="p-4 rounded-lg bg-slate-100 shadow grow">
        <div>
            <span class='block font-medium text-sm text-gray-700'>{{ __("Availability") }}</span>

            @foreach($productVariant->availability as $availability)
                <div class="flex flex-row justify-between">
                    <div>
                        <span>{{ $availability->availabilityType->translation() }}</span>
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
        </div>
    </div>
</div>

<div class="flex flex-row justify-between">
    <div class="p-4 rounded-lg bg-slate-100 shadow grow">

    </div>
</div>
