<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @return BelongsTo<Brand>
     */
    public function brand(): BelongsTo
    {
        return $this->BelongsTo(Brand::class);
    }

    /**
     * @return BelongsTo<Category>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ProductVariant>
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
