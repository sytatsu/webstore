@props(['availability'])

<div {{ $attributes->onlyProps(['class'])->merge(['class' => 'py-1']) }}>
    @if($availability->availabilityType === \App\Enums\AvailabilityEnum::STOCK)
        <span class="{{ $availability->availabilityQuantity ? 'bg-green-600' : 'bg-red-500' }} rounded-lg text-white py-1 px-2 mr-2">{{ $availability->availabilityQuantity }}</span>
    @endif

    <div class="inline">
        <span class="avenir-bold">{{ $availability->availabilityType->translation() }}</span>

        @if($availability->availabilityType === \App\Enums\AvailabilityEnum::STOCK)
            <span>@ {{ $availability->availabilityLocation->label }}</span>
        @endif
    </div>
</div>
