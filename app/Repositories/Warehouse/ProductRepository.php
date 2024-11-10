<?php

namespace App\Repositories\Warehouse;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function all(?array $withRelations): Collection
    {
        return Product::with($withRelations ?? [])->get();
    }

    public function find(string $uuid): ?Product
    {
        return Product::find($uuid);
    }
}
