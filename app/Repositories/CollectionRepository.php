<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Collection;
use Lunar\Models\Collection as LunarCollection;

readonly class CollectionRepository
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    /**
     * @param array<int> $collectionIds
     * @return Collection<LunarCollection>
     */
    public function getWithLimitedProducts(array $collectionIds, int $maxProducts = 4): Collection
    {
        return LunarCollection::query()
            ->whereIn('id', $collectionIds)
            ->with(['products' => function ($query) use ($maxProducts) {
                $this->productRepository->applyOrdering($query->getQuery())
                    ->orderBy('lunar_products.created_at', 'desc')
                    ->limit($maxProducts);

                // Add pivot columns to groupBy for collection relationship
                $query->groupBy('lunar_collection_product.position', 'lunar_collection_product.created_at', 'lunar_collection_product.updated_at');
            }])
            ->get();
    }

    public function find(int $id): ?LunarCollection
    {
        return LunarCollection::find($id);
    }

    public function getCollectionWithDescendants(LunarCollection $collection): Collection
    {
        $collections = $collection->descendants->toFlatTree();
        $collections->add($collection);

        return $collections;
    }

    public function getTree(): Collection
    {
        return LunarCollection::query()
            ->with(['children', 'defaultUrl', 'children.defaultUrl'])
            ->whereIsRoot()
            ->defaultOrder()
            ->get();
    }

    /**
     * @param array<string> $groupHandles
     * @return Collection<LunarCollection>
     */
    public function getTreeByGroupHandles(array $groupHandles): Collection
    {
        return LunarCollection::query()
            ->with(['children', 'defaultUrl', 'children.defaultUrl', 'group'])
            ->whereHas('group', function ($query) use ($groupHandles) {
                $query->whereIn('handle', $groupHandles);
            })
            ->whereIsRoot()
            ->defaultOrder()
            ->get();
    }
}
