<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as IlluminateCollection;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class WebstoreHelperService
{
    private const string ROUTE_PRODUCT    = 'sytatsu.webstore.product';
    private const string ROUTE_COLLECTION = 'sytatsu.webstore.collection';

    public static function priceRangeString(IlluminateCollection $priceCollection): string
    {
        if ($priceCollection->isEmpty()) {
            return __('N/A');
        }

        $uniquePrices = $priceCollection->unique('price');

        if ($uniquePrices->count() === 1) {
            return $uniquePrices->first()->price->formatted();
        }

        return sprintf(
            '%s %s',
            __('From'),
            $uniquePrices->sortBy('price')->first()->price->formatted()
        );
    }

    public static function productOptionsArray(Product $product): array
    {
        if ($product->productOptions->isEmpty()) {
            return [];
        }

        return $product->productOptions->mapWithKeys(function (ProductOption $productOption) {
            $mappedValues = $productOption->values->map(static fn(ProductOptionValue $value) => [
                'id'   => $value->id,
                'name' => $value->translate('name')
            ]);

            return [$productOption->translate('name') => $mappedValues];
        })->toArray();
    }

    public static function productPlaceholderImage(): string
    {
        return 'https://dummyimage.com/400x400/a5dcf3/ffffff&text=Coming Soon';
        // return \Vite::asset('resources/images/product_placeholder.jpg');
    }

    public static function getProductRoute(Product $product, array $parameters = []): string
    {
        return self::getRoute(
            model: $product,
            routeName: self::ROUTE_PRODUCT,
            parameterKey: 'product',
            parameters: $parameters
        );
    }

    public static function getCollectionRoute(Collection $collection, array $parameters = []): string
    {
        return self::getRoute(
            model: $collection,
            routeName: self::ROUTE_COLLECTION,
            parameterKey: 'collection',
            parameters: $parameters
        );
    }

    /**
     * Breadcrumb items (name + item URL) for a collection, preceded by its
     * parent when it is a sub-collection. A sub-collection links to its
     * parent's page with the subCollections filter applied, the same way
     * the product page already linked them, rather than to its own page.
     */
    public static function getCollectionBreadcrumbItems(Collection $collection): array
    {
        if (!$collection->parent) {
            return [[
                'name' => $collection->translateAttribute('name'),
                'item' => self::getCollectionRoute($collection),
            ]];
        }

        return [
            [
                'name' => $collection->parent->translateAttribute('name'),
                'item' => self::getCollectionRoute($collection->parent),
            ],
            [
                'name' => $collection->translateAttribute('name'),
                'item' => self::getCollectionRoute($collection->parent, ['subCollections' => [$collection->id]]),
            ],
        ];
    }

    private static function getRoute(Model $model, string $routeName, string $parameterKey, array $parameters = []): string
    {
        $defaultUrl = $model->defaultUrl;
        if (!$defaultUrl?->slug) {
            $modelType = class_basename($model);
            throw new \InvalidArgumentException("{$modelType} must have a default URL with slug");
        }

        return route($routeName, $parameters + [$parameterKey => $defaultUrl->slug]);
    }

}
