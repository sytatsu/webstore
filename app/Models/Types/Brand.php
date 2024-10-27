<?php

namespace App\Models\Types;

use App\Models\Warehouse\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * -- Fields
 * @property string $uuid
 * @property string $name
 * @property string $description
 *
 * -- Relations
 * @property array<Product> $products
 */
class Brand extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'uuid';
    protected $table = 'type_brands';

    protected $fillable = [
        'name',
        'description',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
