<?php

namespace App\DataTransformers;

use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class ProductTransformer
{
    public static function productOptionsArray(Product $product): array
    {
        return $product->productOptions->mapWithKeys(function (ProductOption $productOption) {
            return [$productOption->translate('name') => $productOption->values->map(fn (ProductOptionValue $value) => [
                'id' => $value->id,
                'name' => $value->translate('name')
            ])];
        })->toArray();
    }
}
