<?php

namespace App\DataTransformers;


use Lunar\Models\Collection;
use Lunar\Models\Product;

class RouteTransformer
{
    static public function getProductRoute(Product $product, array $parameters = []): string
    {
        return route('sytatsu.webstore.product', array_merge($parameters, [
            'product' => $product->defaultUrl->slug,
        ]));
    }

    static public function getCollectionRoute(Collection $collection, array $parameters = []): string
    {
        return route('sytatsu.webstore.collection', array_merge($parameters, [
            'collection' => $collection->defaultUrl->slug
        ]));
    }
}
