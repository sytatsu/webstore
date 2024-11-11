<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Enums\AvailabilityEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string           $uuid
 * @property string           $name
 * @property string           $description
 * @property int              $price
 * @property string           $sku
 * @property AvailabilityEnum $availability_type
 * @property int              $availability_quantity
 *
 * @property Product             $product
 * @property Collection<Variant> $variant
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
        'availability_type',
        'availability_quantity'
    ];

    protected $casts = [
        'price' => CurrencyCast::class,
        'availability_type' => AvailabilityEnum::class,
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
}
