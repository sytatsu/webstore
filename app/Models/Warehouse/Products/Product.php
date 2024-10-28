<?php

namespace App\Models\Warehouse\Products;

use App\Models\Types\Brand;
use App\Models\Types\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string                $uuid
 *
 * @property Brand                 $brand,
 * @property Category              $category
 * @property array<ProductVariant> $productVariants
 */
class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'uuid';

    protected $table = 'warehouse_products';

    protected $fillable = [];

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
     * @return BelongsToMany<\App\Models\Warehouse\Products\ProductVariant>
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class);
    }
}
