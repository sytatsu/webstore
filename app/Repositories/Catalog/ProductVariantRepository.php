<?php

namespace App\Repositories\Catalog;

use App\DataObject\Currency;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;

class ProductVariantRepository
{
    public function all(?array $withRelations): Collection
    {
        return ProductVariant::with($withRelations ?? [])->get();
    }

    public function find(string $uuid): ?ProductVariant
    {
        return ProductVariant::find($uuid);
    }

    public function fill(ProductVariant $productVariant, Product $product, array $data): ProductVariant
    {
        $productVariant->name = $data['name'];
        $productVariant->description = $data['description'];
        $productVariant->price = Currency::from(price: $data['price']);
        $productVariant->sku = $data['sku'];

        $productVariant->product()->associate($product);

        return $productVariant;
    }

    public function save(ProductVariant $productVariant): ProductVariant
    {
        $productVariant->save();
        return $productVariant;
    }

    public function syncVariants(ProductVariant $productVariant, array $variants): ProductVariant
    {
        $productVariant->variants()->sync(array_column($variants, 'uuid'));
        return $productVariant;
    }

    public function syncChannel(ProductVariant $productVariant, array $channel): ProductVariant
    {
        $productVariant->channel()->sync(array_column($channel, 'uuid'));
        return $productVariant;
    }
}
