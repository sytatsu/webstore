<?php

namespace App\Repositories\Catalog;

use App\Models\Channel;
use App\Models\ChannelLocation;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ChannelRepository
{
    public function all(?array $withRelations): Collection
    {
        return Channel::with($withRelations ?? [])->get();
    }

    public function find(string $uuid): ?Channel
    {
        return Channel::find($uuid);
    }

    public function fill(Channel $channel, ChannelLocation $channelLocation, array $data): Channel
    {
        $channel->channelType = $data['channel_type'];
        $channel->channelQuantity = $data['channel_quantity'];

        $channel->channelLocation()->associate($channelLocation);

        return $channel;
    }

    public function save(Channel $channel): Channel
    {
        $channel->save();
        return $channel;
    }
}
