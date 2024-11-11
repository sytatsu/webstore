<?php

namespace App\Repositories\Warehouse;

use App\Models\Brand;
use App\Models\Category;
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

    public function fill(Product $product, Category $category, Brand $brand, array $data): Product
    {
        $product->name = $data['name'];
        $product->description = $data['description'] ?? '';
        $product->productType = $data['product_type'];
        $product->productVariantType = $data['product_variant_type'];
        $product->hasMultipleVariants = $data['has_multiple_variants'] ?? $data['product_variant_type'] === 'GENERIC';

        $product->category()->associate($category);
        $product->brand()->associate($brand);

        return $product;
    }

    public function save(Product $product): Product
    {
        $product->save();
        return $product;
    }
}
