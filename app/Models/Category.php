<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property string $parent_category_uuid
 * @property Collection<Product> $products
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

    protected $with = [
        'childCategories',
    ];

    /**
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productCount(): int
    {
        return $this->products->count();
    }

    /**
     * @return BelongsTo
     */
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(related: Category::class, foreignKey: 'parent_category_uuid');
    }

    public function childCategories(): hasMany
    {
        return $this->hasMany(related: Category::class, foreignKey: 'parent_category_uuid');
    }
}
