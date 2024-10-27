<?php

namespace App\Models\Types;

use App\Models\Warehouse\Products\ProductVariant;
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
 * @property array<ProductVariant> $productVariants
 */
class Variant extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'uuid';

    protected $table = 'type_variants';

    protected $fillable = [
        'name',
        'description',
    ];

    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class);
    }
}
