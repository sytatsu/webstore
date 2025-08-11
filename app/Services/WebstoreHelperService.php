<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class WebstoreHelperService
{
    private string $productRouteName = 'sytatsu.webstore.product';
    private string $collectionRouteName = 'sytatsu.webstore.collection';

    /**
     * @param \Illuminate\Support\Collection<\Lunar\Models\Price> $priceCollection
     */
    public static function priceRangeString(Collection $priceCollection): string
    {
        $priceCollection = $priceCollection->unique('price');

        if ($priceCollection->count() <= 1) {
            return $priceCollection->first()?->price->formatted() ?? 'N/A';
        }

        return sprintf(
            '%s %s',
            __('From'),
            $priceCollection->sortBy('price')->first()->price->formatted()
        );
    }

    public static function productOptionsArray(Product $product): array
    {
        return $product->productOptions->mapWithKeys(function (ProductOption $productOption) {
            return [$productOption->translate('name') => $productOption->values->map(fn (ProductOptionValue $value) => [
                'id' => $value->id,
                'name' => $value->translate('name')
            ])];
        })->toArray();
    }

    public static function getProductRoute(Product $product, array $parameters = []): string
    {
        return route('sytatsu.webstore.product', array_merge($parameters, [
            'product' => $product->defaultUrl->slug,
        ]));
    }

    public static function getCollectionRoute(\Lunar\Models\Collection $collection, array $parameters = []): string
    {
        return route('sytatsu.webstore.collection', array_merge($parameters, [
            'collection' => $collection->defaultUrl->slug
        ]));
    }
}
