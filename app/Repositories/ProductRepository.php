<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Lunar\Models\Product;

class ProductRepository
{

    /**
     * @param array<int>|Collection<int> $collectionIds
     * @param array $filters
     * @return Collection<Product>
     */
    public function getFilteredProducts(array|Collection $collectionIds, ?int $limit = null, array $filters = []): Collection
    {
        $query = Product::query();

        $query->select('lunar_products.*')
            ->join('lunar_collection_product', 'lunar_products.id', '=', 'lunar_collection_product.product_id')
            ->whereIn('lunar_collection_product.collection_id', $collectionIds);

        if (!empty($filters['minPrice']) || !empty($filters['maxPrice']) || !empty($filters['inStock'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                if (!empty($filters['minPrice']) || !empty($filters['maxPrice'])) {
                    // Note: We use a subquery or different join if we needed to avoid groupBy here,
                    // but whereHas already handles it correctly without affecting the main query's SELECT list.
                    $q->join('lunar_prices', 'lunar_product_variants.id', '=', 'lunar_prices.priceable_id')
                        ->where('lunar_prices.priceable_type', \Lunar\Models\ProductVariant::class);

                    if (!empty($filters['minPrice'])) {
                        $q->where('lunar_prices.price', '>=', $filters['minPrice'] * 100);
                    }

                    if (!empty($filters['maxPrice'])) {
                        $q->where('lunar_prices.price', '<=', $filters['maxPrice'] * 100);
                    }
                }

                if (!empty($filters['inStock'])) {
                    $q->where('purchasable', 'in_stock')->where('stock', '>', 0);
                }
            });
        }

        // Apply in-stock ordering as a primary sort criterion
        $this->applyOrdering($query);

        $sort = $filters['sort'] ?? 'alphabetical';

        switch ($sort) {
            case 'newest':
                $query->orderBy('lunar_products.created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('lunar_products.created_at', 'asc');
                break;
            case 'alphabetical':
            default:
                $query->orderByRaw("LOWER(COALESCE(
                    json_unquote(json_extract(lunar_products.attribute_data, '$.name.value')),
                    json_unquote(json_extract(lunar_products.attribute_data, '$.name.en')),
                    json_unquote(json_extract(lunar_products.attribute_data, '$.name.nl')),
                    json_unquote(json_extract(lunar_products.attribute_data, '$.name')),
                    ''
                )) ASC");
                break;
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getOrderedByCreatedAt(array|Collection $collectionIds, ?int $limit = null): Collection
    {
        $query = Product::query();

        $query->select('lunar_products.*')
            ->join('lunar_collection_product', 'lunar_products.id', '=', 'lunar_collection_product.product_id')
            ->whereIn('lunar_collection_product.collection_id', $collectionIds);

        $this->applyOrdering($query)
            ->orderBy('lunar_products.created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getByIds(array $ids): Collection
    {
        return Product::whereIn('id', $ids)->get();
    }

    public function applyOrdering(Builder $query): Builder
    {
        return $query->addSelect([
            'in_stock_order' => \Lunar\Models\ProductVariant::selectRaw("MIN(CASE WHEN purchasable = 'in_stock' AND stock > 0 THEN 0 ELSE 1 END)")
                ->whereColumn('product_id', 'lunar_products.id')
                ->limit(1)
        ])
            ->orderBy('in_stock_order', 'asc');
    }
}
