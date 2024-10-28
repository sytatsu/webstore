<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property string $parent_type_category_uuid
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
        'parent_type_category_uuid',
    ];

    /**
     * @return BelongsToMany<Product>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * @return null|HasOne<self>
     */
    public function parentCategory(): ?HasOne
    {
        return $this->parent_type_category_uuid
            ? $this->hasOne(related: Category::class, localKey: 'parent_type_category_uuid',)
            : null;
    }
}
