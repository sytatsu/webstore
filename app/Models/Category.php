<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property string $parent_category_uuid
 * @property array<Product> $products
 * @property ?self $parentCategory
 */
class Category extends BaseModel
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'description',
        'parent_category_uuid',
    ];

    /**
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return null|BelongsTo<self>
     */
    public function parentCategory(): ?BelongsTo
    {
        return $this->parent_category_uuid
            ? $this->belongsTo(related: Category::class, foreignKey: 'parent_category_uuid')
            : null;
    }
}
