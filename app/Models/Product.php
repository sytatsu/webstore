<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string                $uuid
 *
 * @property Brand                 $brand,
 * @property Category              $category
 * @property boolean               $hasMultipleVariants
 * @property array<ProductVariant> $productVariants
 */
class Product extends BaseModel
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'description',
        'has_multiple_variants',
        'product_type',
        'product_variant_type',
        'discontinued_at'
    ];

    protected $casts = [
        'has_multiple_variants' => 'boolean',
        'product_type' => ProductTypeEnum::class,
        'product_variant_type' => ProductVariantType::class,
        'discontinued_at' => 'timestamp'
    ];

    /**
     * @return HasOne<Brand>
     */
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class);
    }

    /**
     * @return HasOne<Category>
     */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }

    /**
     * @return BelongsToMany<ProductVariant>
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class);
    }
}
