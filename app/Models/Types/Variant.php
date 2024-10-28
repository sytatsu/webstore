<?php

namespace App\Models\Types;

use App\Models\Warehouse\Products\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property array<ProductVariant> $productVariants
 */
class Variant extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $table = 'type_variants';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsToMany<ProductVariant>
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class);
    }
}
