<?php

namespace App\Models\Types;

use App\Models\Warehouse\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property array<Product> $products
 */
class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';
    protected $table = 'type_brands';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsToMany<Product>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
