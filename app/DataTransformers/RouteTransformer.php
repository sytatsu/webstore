<?php

namespace App\DataTransformers;


use Lunar\Models\Product;

class RouteTransformer
{
    static public function getProductRoute(Product $product, array $parameters = [])
    {
        return route('sytatsu.webstore.product', array_merge($parameters, [
            'product' => $product->defaultUrl->slug,
        ]));
    }
}
