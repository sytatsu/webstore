<?php

namespace App\Services\Catalog;

use App\Enums\ProductVariantType;
use App\Models\Channel;
use App\Models\ChannelLocation;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Repositories\Catalog\ChannelLocationRepository;
use App\Repositories\Catalog\ChannelRepository;
use App\Repositories\Catalog\ProductRepository;
use App\Repositories\Catalog\ProductVariantRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChannelService
{

    public function __construct(
        private readonly ChannelRepository $channelRepository,
        private readonly ChannelLocationRepository $channelLocationRepository,
    ) {
        //
    }

    public function getLocationList(): SupportCollection
    {
        return $this->channelLocationRepository->all()->pluck('label', 'label');
    }

    public function newChannel(): Channel
    {
        return new Channel();
    }

    public function newChannelLocation(): ChannelLocation
    {
        return new ChannelLocation();
    }

    public function channelLocationFirstByLabelOrCreate(string $label): ChannelLocation
    {
        return $this->channelLocationRepository->findByLabel(label: $label)
            ?? $this->storeChannelLocation(channelLocation: null, data: ['label' => $label]);
    }

    public function storeChannel(?Channel $channel, ?ChannelLocation $channelLocation, array $data): Channel
    {
        if ( ! isset($channelLocation)) {
            $channelLocation = $this->channelLocationFirstByLabelOrCreate(label: $data['location']['label'] ?? 'N/A');
        }

        if ( ! isset($channel)) {
            $channel = $this->newChannel();
        }

        $this->channelRepository->fill(
            channel: $channel,
            channelLocation: $channelLocation,
            data: $data,
        );

        $this->channelRepository->save($channel);

        return $channel;
    }

    public function storeChannelLocation(?ChannelLocation $channelLocation, array $data): ChannelLocation {
        if ( ! isset($channelLocation)) {
            $channelLocation = $this->newChannelLocation();
        }

        $this->channelLocationRepository->fill(
            channelLocation: $channelLocation,
            data: $data
        );

        $this->channelLocationRepository->save($channelLocation);

        return $channelLocation;
    }
}
