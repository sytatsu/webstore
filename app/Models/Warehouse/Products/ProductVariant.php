<?php

namespace App\Models\Warehouse\Products;

use App\Models\Types\Variant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * -- Fields
 * @property string $uuid
 *
 * -- Relations
 */
class ProductVariant extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $table = 'warehouse_product_variants';

    protected $fillable = [];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    public function variant(): HasOne
    {
        return $this->hasOne(Variant::class);
    }
}
