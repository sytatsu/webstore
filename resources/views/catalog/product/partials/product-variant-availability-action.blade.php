@props(['productVariant'])

@if(!$productVariant->availability->filter(fn($availability) => $availability->availabilityType ===  \App\Enums\AvailabilityEnum::STOCK)->isEmpty())
    <x-actions.button>
        <i class="fa fa-box pr-1"></i><span>{{ __('Manage Stock') }}</span>
    </x-actions.button>
@endif
