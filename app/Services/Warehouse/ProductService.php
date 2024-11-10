<?php

namespace App\Services\Warehouse;

use App\Enums\ProductVariantType;
use App\Models\Product;
use App\Repositories\Warehouse\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
        //
    }

    public function getProductList(): Collection
    {
        return $this->productRepository->all(withRelations: [
            'brand',
            'category',
            'productVariants',
            'productVariants.variant',
        ]);
    }

    public function getProduct(string|Product $product): Product
    {
        if (is_string($product)) {
            $product = $this->productRepository->find(uuid: $product)
                ?? throw new NotFoundHttpException();
        }

        $product->load([
            'brand',
            'category',
            'productVariants',
            'productVariants.variant',
        ]);

        return $product;
    }

    public function productVariantTypeCountToString(Product $product): string
    {
        return $product->productVariantType  === ProductVariantType::UNIQUE
            ? $product->productVariantType->translation()
            : $product->productVariants->count();
    }

    public function productVariantTypePriceToString(Product $product): string
    {
        return $product->productVariantType  === ProductVariantType::UNIQUE || $product->productVariants->count() <= 1
            ? $product->productVariants->first()?->price->formatted() ?? 'N/A'
            : sprintf('%s - %s',
                $product->productVariants->sortBy('price')->first()?->price->formatted(),
                $product->productVariants->sortByDesc('price')->first()?->price->string()
            );
    }
}
