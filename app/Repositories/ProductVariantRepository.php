<?php

declare(strict_types=1);

namespace App\Repositories;

use Lunar\Models\ProductVariant;

class ProductVariantRepository
{
    public function find(int|string $id): ?ProductVariant
    {
        return ProductVariant::find($id);
    }
}
