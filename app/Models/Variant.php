<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string                     $uuid
 * @property string                     $name
 * @property string                     $description
 * @property string                     $parent_variant_uuid
 * @property Collection<ProductVariant> $productVariants
 */
class Variant extends BaseModel
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'description',
        'parent_variant_uuid',
    ];

    /**
     * @return BelongsToMany<ProductVariant>
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ProductVariant::class,
            table: 'product_variants_many_variants',
            foreignPivotKey: 'variant_uuid',
            relatedPivotKey: 'product_variant_uuid',
        );
    }

    public function productVariantCount(): int
    {
        return $this->productVariants()->count();
    }

    /**
     * @return BelongsTo
     */
    public function parentVariant(): BelongsTo
    {
        return $this->belongsTo(related: Variant::class, foreignKey: 'parent_variant_uuid');
    }

    public function childVariants(): HasMany
    {
        return $this->hasMany(related: Variant::class, foreignKey: 'parent_variant_uuid');
    }
}
