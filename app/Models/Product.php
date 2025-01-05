<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string                     $uuid
 * @property string                     $name
 * @property string                     $description
 * @property ?Carbon                    $discontinuedAt
 *
 * @property Brand                      $brand,
 * @property Category                   $category
 * @property Collection<ProductVariant> $productVariants
 * @property ProductVariantType         $productVariantType
 * @property ProductTypeEnum            $productType
 */
class Product extends BaseModel
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'description',
        'product_type',
        'product_variant_type',
        'discontinued_at'
    ];

    protected $casts = [
        'product_type' => ProductTypeEnum::class,
        'product_variant_type' => ProductVariantType::class,
        'discontinued_at' => 'datetime'
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

    public function productVariantCount(): int
    {
        return $this->productVariants()->count();
    }
}
