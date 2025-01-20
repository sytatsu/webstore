@props(['channel'])

<div {{ $attributes->onlyProps(['class'])->merge(['class' => 'py-1']) }}>
    @if($channel->channelType === \App\Enums\ChannelEnum::STOCK)
        <span class="{{ $channel->channelQuantity ? 'bg-green-600' : 'bg-red-500' }} rounded-lg text-white py-1 px-2 mr-2">{{ $channel->channelQuantity }}</span>
    @endif

    <div class="inline">
        <span class="avenir-bold">{{ $channel->channelType->translation() }}</span>

        @if($channel->channelType === \App\Enums\ChannelEnum::STOCK)
            <span>@ {{ $channel->channelLocation->label }}</span>
        @endif
    </div>
</div>
