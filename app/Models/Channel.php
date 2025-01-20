<?php

namespace App\Models;

use App\Enums\ChannelEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $channelQuantity
 * @property mixed $channelType
 */
class Channel extends BaseModel
{
    use HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'channel_type',
        'channel_quantity',
        'channel_location_uuid',
    ];
    protected $with = [
        'channelLocation'
    ];

    protected $casts = [
        'channel_type' => ChannelEnum::class
    ];

    public function channelLocation(): BelongsTo
    {
        return $this->belongsTo(ChannelLocation::class, 'channel_location_uuid');
    }

    /**
     * @return BelongsToMany<ProductVariant>
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ProductVariant::class,
            table: 'product_variants_many_channels',
            foreignPivotKey: 'channel_uuid',
            relatedPivotKey: 'product_variant_uuid',
        );
    }
}
