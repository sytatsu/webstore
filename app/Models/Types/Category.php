<?php

namespace App\Models\Types;

use App\Models\Warehouse\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * -- Fields
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property string $parent_type_category_uuid
 *
 * -- Relations
 * @property array<Product> $products
 * @property ?self $parentCategory
 */
class Category extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'uuid';
    protected $table = 'type_categories';

    protected $fillable = [
        'name',
        'description',
        'parent_type_category_uuid',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function parentCategory(): ?HasOne
    {
        return $this->parent_type_category_uuid
            ? $this->hasOne(related: Category::class, localKey: 'parent_type_category_uuid',)
            : null;
    }
}
