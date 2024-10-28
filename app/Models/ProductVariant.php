<?php

namespace App\Models;

use App\Enums\AvailabilityEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $uuid
 *
 * @property Product $product
 * @property Variant $variant
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
        'availability_type' => AvailabilityEnum::class,
    ];

    /**
     * @return HasOne<Product>
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    /**
     * @return HasOne<Variant>
     */
    public function variant(): HasOne
    {
        return $this->hasOne(Variant::class);
    }
}
