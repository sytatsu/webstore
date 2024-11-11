<?php

namespace App\Services\Warehouse;

use App\Enums\ProductVariantType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Repositories\Warehouse\ProductRepository;
use App\Repositories\Warehouse\ProductVariantRepository;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductVariantRepository $productVariantRepository,
        private readonly BrandService $brandService,
        private readonly CategoryService $categoryService,
        private readonly VariantService $variantService,
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
        if ($product->productVariantType  === ProductVariantType::UNIQUE || $product->productVariants->count() <= 1) {
            return $product->productVariants->first()?->price->formatted() ?? 'N/A';
        }

        $lowestPrice = $product->productVariants->sortBy('price')->first()?->price;
        $highestPrice = $product->productVariants->sortByDesc('price')->first()?->price;

        if ($lowestPrice->integer() === $highestPrice->integer()) {
            return $lowestPrice->formatted();
        }

        return "{$lowestPrice->formatted()} - {$highestPrice->string()}";
    }

    public function newProduct(): Product
    {
        return new Product();
    }

    public function newProductVariant(): ProductVariant
    {
        return new ProductVariant();
    }

    public function storeProduct(?Product $product, Category $category, Brand $brand, array $data): Product
    {
        if (!isset($product)) {
            $product = $this->newProduct();
        }

        $this->productRepository->fill(
            product: $product,
            category: $category,
            brand: $brand,
            data: $data,
        );

        $this->productRepository->save($product);
        return $product;
    }

    public function storeProductVariant(?ProductVariant $productVariant, Product $product, Variant $variant, array $data): ProductVariant
    {
        if (!isset($productVariant)) {
            $productVariant = $this->newProductVariant();
        }

        $this->productVariantRepository->fill(
            productVariant: $productVariant,
            product: $product,
            variant: $variant,
            data: $data,
        );

        $this->productVariantRepository->save($productVariant);
        return $productVariant;
    }

    public function importProductFromArray(array $productArray): Product
    {
        $brand = $this->brandService->findByNameOrCreate(name: $productArray['brand']);
        $category = $this->categoryService->findByNameOrCreate(name: $productArray['category']);

        // Create main product
        $product = $this->storeProduct(product: null, category: $category, brand: $brand, data: $productArray);

        foreach ($productArray['product_variants'] as $productVariantArray) {

            if (isset($productVariantArray['variant']['$parentVariant']['name'])) {
                $parentVariant = $this->variantService->findByNameOrCreate(name: $productVariantArray['variant']['parent_variant']['name']);
            }
            $variant = $this->variantService->findByNameOrCreate(name: $productVariantArray['variant']['name'], parentVariant: $parentVariant ?? null);

            $this->storeProductVariant(productVariant: null, product: $product, variant: $variant, data: $productVariantArray);
        }

        return $product;
    }
}
