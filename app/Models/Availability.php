<?php

namespace App\Models;

use App\Enums\AvailabilityEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $availabilityQuantity
 * @property mixed $availabilityType
 */
class Availability extends BaseModel
{
    use HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'availability_type',
        'availability_quantity',
        'availability_location_uuid',
    ];
    protected $with = [
        'availabilityLocation'
    ];

    protected $casts = [
        'availability_type' => AvailabilityEnum::class
    ];

    public function availabilityLocation(): BelongsTo
    {
        return $this->belongsTo(AvailabilityLocation::class, 'availability_location_uuid');
    }

    /**
     * @return BelongsToMany<ProductVariant>
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ProductVariant::class,
            table: 'product_variants_many_availabilities',
            foreignPivotKey: 'availability_uuid',
            relatedPivotKey: 'product_variant_uuid',
        );
    }
}
