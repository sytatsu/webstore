<?php

namespace App\Services\Warehouse;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getProducts(): Collection
    {
        return Product::with('productVariants')->get();
    }
}
