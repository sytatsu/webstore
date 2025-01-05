<?php

namespace App\Services\Warehouse;

use App\Enums\ProductVariantType;
use App\Models\Availability;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Repositories\Warehouse\ProductRepository;
use App\Repositories\Warehouse\ProductVariantRepository;
use Carbon\Carbon;
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
        private readonly AvailabilityService $availabilityService,
    ) {
        //
    }

    public function getProducts(): Collection
    {
        return $this->productRepository->all(withRelations: [
            'brand',
            'category',
            'productVariants',
            'productVariants.variants',
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
            'productVariants.variants',
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

    public function deleteProduct(string $uuid): void
    {
        $product = $this->getProduct($uuid);

        $product->productVariants()->each(function (ProductVariant $productVariant) {
            $productVariant->availability()->each(fn (Availability $availability) => $availability->delete());
            $productVariant->delete();
        });

        $product->delete();
    }

    public function discontinueProduct(string $uuid): Product
    {
        $product = $this->getProduct($uuid);

        $product->discontinuedAt = Carbon::now();
        $product->save();

        return $product;
    }

    public function continueProduct(string $uuid): Product
    {
        $product = $this->getProduct($uuid);

        $product->discontinuedAt = null;
        $product->save();

        return $product;
    }

    public function storeProductVariant(?ProductVariant $productVariant, Product $product, array $variants, array $availability, array $data): ProductVariant
    {
        if (!isset($productVariant)) {
            $productVariant = $this->newProductVariant();

            if (!isset($data['name'])) {
                $data['name'] = implode(' | ', array_map(fn($variant) => $variant->name, $variants)) ?? 'N/A';
            }
        }

        $this->productVariantRepository->fill(
            productVariant: $productVariant,
            product: $product,
            data: $data,
        );

        $this->productVariantRepository->save($productVariant);
        $this->productVariantRepository->syncVariants($productVariant, $variants);

        if ($availability) {
            $this->productVariantRepository->syncAvailability($productVariant, $availability);
        }

        return $productVariant;
    }

    public function importProductFromArray(array $productArray): Product
    {
        $brand = $this->brandService->findByNameOrCreate(name: $productArray['brand']);
        $category = $this->categoryService->findByNameOrCreate(name: $productArray['category']);

        // Create main product
        $product = $this->storeProduct(product: null, category: $category, brand: $brand, data: $productArray);

        foreach ($productArray['product_variants'] as $productVariantArray) {

            $variants = array_map(function ($variantArray) {
                if (isset($variantArray['parent_variant']['name'])) {
                    $parentVariant = $this->variantService->findByNameOrCreate(name: $variantArray['parent_variant']['name']);
                }

                return $this->variantService->findByNameOrCreate(name: $variantArray['name'], parentVariant: $parentVariant ?? null);
            }, $productVariantArray['variants']);
            $availability = array_map(function ($availabilityArray) {
                return $this->availabilityService->storeAvailability(availability: null, availabilityLocation: null, data: $availabilityArray);
            }, $productVariantArray['availability']);

            $this->storeProductVariant(productVariant: null, product: $product, variants: $variants, availability: $availability, data: $productVariantArray);
        }

        return $product;
    }
}
