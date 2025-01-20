@props(['productVariant'])

@if(!$productVariant->channel->filter(fn($channel) => $channel->channelType ===  \App\Enums\ChannelEnum::STOCK)->isEmpty())
    <x-actions.button>
        <i class="fa fa-box pr-1"></i><span>{{ __('Manage Stock') }}</span>
    </x-actions.button>
@endif
