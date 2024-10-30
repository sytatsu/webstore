<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string                $uuid
 * @property string                $name
 * @property string                $description
 * @property string                $parent_variant_uuid
 * @property array<ProductVariant> $productVariants
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
     * @return HasMany<ProductVariant>
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * @return null|BelongsTo<self>
     */
    public function parentCategory(): ?BelongsTo
    {
        return $this->parent_variant_uuid
            ? $this->belongsTo(related: Variant::class, foreignKey: 'parent_variant_uuid')
            : null;
    }
}
