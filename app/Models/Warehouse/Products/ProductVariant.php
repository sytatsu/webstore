<?php

namespace App\Models\Warehouse\Products;

use App\Models\Types\Variant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $uuid
 *
 * @property Product $product
 * @property Variant $variant
 */
class ProductVariant extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $table = 'warehouse_product_variants';

    protected $fillable = [];

    /**
     * @return HasOne<Product>
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    /**
     * @return HasOne<Variant>
     */
    public function variant(): HasOne
    {
        return $this->hasOne(Variant::class);
    }
}
