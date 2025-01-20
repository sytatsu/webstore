<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Enums\ChannelEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string              $uuid
 * @property string              $name
 * @property string              $description
 * @property int                 $price
 * @property string              $sku
 * @property ChannelEnum    $channel_type
 * @property int                 $channel_quantity
 *
 * @property Product             $product
 * @property Collection<Variant> $variant
 * @property Collection<Channel> $channel
 */
class ProductVariant extends BaseModel
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
    ];

    protected $casts = [
        'price' => CurrencyCast::class,
    ];

    protected $with = [
        'channel'
    ];

    /**
     * @return BelongsTo<Product>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsToMany<Variant>
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Variant::class,
            table: 'product_variants_many_variants',
            foreignPivotKey: 'product_variant_uuid',
            relatedPivotKey: 'variant_uuid',
        );
    }

    /**
     * @return BelongsToMany<Variant>
     */
    public function channel(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Channel::class,
            table: 'product_variants_many_channels',
            foreignPivotKey: 'product_variant_uuid',
            relatedPivotKey: 'channel_uuid',
        );
    }
}
