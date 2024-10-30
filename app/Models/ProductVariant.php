<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Enums\AvailabilityEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * @return BelongsTo<Variant>
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
