<?php

namespace App\Repositories\Catalog;

use App\Models\Channel;
use App\Models\ChannelLocation;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ChannelLocationRepository
{
    public function all(): Collection
    {
        return ChannelLocation::all();
    }

    public function find(string $uuid): ?ChannelLocation
    {
        return ChannelLocation::find($uuid);
    }

    public function findByLabel(string $label): ?ChannelLocation
    {
        return ChannelLocation::where('label', $label)->first();
    }

    public function fill(ChannelLocation $channelLocation, array $data): ChannelLocation
    {
        $channelLocation->label = $data['label'];

        return $channelLocation;
    }

    public function save(ChannelLocation $channelLocation): ChannelLocation
    {
        $channelLocation->save();
        return $channelLocation;
    }
}
