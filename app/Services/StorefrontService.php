<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductCollectionDTO;
use App\Repositories\CollectionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use Illuminate\Support\Collection;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

readonly class StorefrontService
{
    public function __construct(
        private ProductRepository $productRepository,
        private CollectionRepository $collectionRepository,
        private ProductVariantRepository $productVariantRepository
    ) {
    }

    public function getProductsForCollections(Collection|array $collectionIds, ?int $limit = null, array $filters = []): Collection
    {
        return $this->productRepository->getFilteredProducts($collectionIds, $limit, $filters);
    }

    public function findVariantByOptions(Product $product, array $selectedOptionValues): ?ProductVariant
    {
        return $product->variants->first(function (ProductVariant $variant) use ($selectedOptionValues) {
            return ! $variant->values->pluck('id')
                ->diff(collect($selectedOptionValues)->values())
                ->count();
        });
    }

    public function getProductOptionsWithValues(Product $product): Collection
    {
        return $product->variants->pluck('values')
            ->flatten()
            ->unique('id')
            ->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
    }

    public function getSelectedOptionsForVariant(ProductVariant $variant): array
    {
        return $variant->values->mapWithKeys(function ($value) {
            return [$value->product_option_id => $value->id];
        })->toArray();
    }

    public function getDefaultSelectedOptions(Product $product): array
    {
        return $this->getProductOptionsWithValues($product)->mapWithKeys(function ($data) {
            return [$data['option']->id => $data['values']->first()->id];
        })->toArray();
    }

    /**
     * @param array<int> $collectionIds
     * @param int $maxProducts
     * @return Collection<ProductCollectionDTO>
     */
    public function getCollectionsWithLimitedProducts(array $collectionIds, int $maxProducts = 4): Collection
    {
        $collections = $this->collectionRepository->getWithLimitedProducts($collectionIds, $maxProducts);

        return $collections->map(function (LunarCollection $collection) {
            return new ProductCollectionDTO(
                collection: $collection,
                products: $collection->products
            );
        });
    }

    /**
     * @param array<int> $collectionIds
     * @param int $maxProducts
     * @return Collection<ProductCollectionDTO>
     */
    public function getCollectionsAndDescendantsWithLimitedProducts(array $collectionIds, int $maxProducts = 4): Collection
    {
        $collections = collect(array_map(function ($collectionId) use ($maxProducts) {
            // @TODO: Feels hacky but works for now. Only used on the welcome page so shouldn't affect performance too much.
            if (!is_numeric($collectionId)) {
                return null;
            }

            $collection = $this->collectionRepository->find((int) $collectionId);
            if (!$collection) {
                return null;
            }
            $collections = $this->getCollectionAndDescendants($collection);
            $collection->products = $this->productRepository->getOrderedByCreatedAt($collections->pluck('id'), $maxProducts);

            return $collection;
        }, $collectionIds))->filter();

        return $collections->map(function (LunarCollection $collection) {
            return new ProductCollectionDTO(
                collection: $collection,
                products: $collection->products
            );
        });
    }

    public function getCollectionAndDescendants(LunarCollection $collection): Collection
    {
        return $this->collectionRepository->getCollectionWithDescendants($collection);
    }

    public function getCollectionTree(): Collection
    {
        return $this->collectionRepository->getTree();
    }

    public function getCollectionTreeByGroupHandles(array $groupHandles): Collection
    {
        return $this->collectionRepository->getTreeByGroupHandles($groupHandles);
    }

    public function getCollectionsBySlugs(array $slugs): Collection
    {
        return $this->collectionRepository->getBySlugs($slugs);
    }

    public function findVariant(int|string $id): ?ProductVariant
    {
        return $this->productVariantRepository->find($id);
    }
}
