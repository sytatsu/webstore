<?php

namespace App\Models\Warehouse\Products;

use App\Models\Types\Brand;
use App\Models\Types\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * -- Fields
 * @property string $uuid
 *
 * -- Relations
 */
class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'uuid';

    protected $table = 'warehouse_products';

    protected $fillable = [];

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class);
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }
}
