<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\Actions\SaveAndAction;
use App\Enums\ProductVariantType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\Product\ProductDeleteRequest;
use App\Http\Requests\Warehouse\Product\ProductDiscontineuRequest;
use App\Http\Requests\Warehouse\Product\ProductStoreRequest;
use App\Http\Requests\Warehouse\Product\ProductUpdateRequest;
use App\Models\Product;
use App\Services\Warehouse\AvailabilityService;
use App\Services\Warehouse\BrandService;
use App\Services\Warehouse\CategoryService;
use App\Services\Warehouse\ProductService;
use App\Services\Warehouse\VariantService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected BrandService $brandService,
        protected CategoryService $categoryService,
        protected VariantService $variantService,
        protected AvailabilityService $availabilityService,
    ) {
    }

    public function list(): Factory|View|Application
    {
        return view("warehouse.product.product-list", [
            'products' => $this->productService->getProducts(),
            'productService' => $this->productService,
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view("warehouse.product.product-create", [
            'brands' => $this->brandService->getBrandList()->toArray(),
            'categories' => $this->categoryService->getCategoryList()->toArray(),
        ]);
    }

    public function edit(Product $product): Factory|View|Application
    {
        return view("warehouse.product.product-edit", [
            'product' => $product,
            'brands' => $this->brandService->getBrandList()->toArray(),
            'categories' => $this->categoryService->getCategoryList()->toArray(),
        ]);
    }

    public function show(Product $product): Factory|View|Application
    {
        return view(view: "warehouse.product.product-show", data: [
            'product' => $this->productService->getProduct($product),
            'productService' => $this->productService,
        ]);
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $product = $this->productService->storeProduct(
            product: null,
            category: $this->categoryService->findByUuid($request->get('category')),
            brand: $this->brandService->findByUuid($request->get('brand')),
            data: $validatedData['product']
        );

        $variants = array_map(function (string $uuid) {
            return $this->variantService->findByUuid($uuid);
        }, $validatedData['product_variant']['variants']) ?? [];

        $availability = $this->availabilityService->storeAvailability(null, null, $validatedData['product_variant']['availability']);

        $this->productService->storeProductVariant(
            productVariant: null,
            product: $product,
            variants: $variants,
            availability: [$availability],
            data: $validatedData['product_variant'],
        );

        return $this->saveAndAction(
            action: $request->get('action'),
            product: $product,
        );
    }

    public function update(ProductUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $product = $this->productService->getProduct($request->get('uuid'));
        $oldProductVariantType = $product->productVariantType;

        $this->productService->storeProduct(
            product: $product,
            category: $this->categoryService->findByUuid($request->get('category')),
            brand: $this->brandService->findByUuid($request->get('brand')),
            data: $validatedData['product']
        );

        if (($oldProductVariantType->value === $product->productVariantType->value) && ($oldProductVariantType->value === ProductVariantType::UNIQUE->value)) {
            $productVariant = $product->productVariants->first();

            $this->productService->storeProductVariant(
                productVariant: $productVariant,
                product: $product,
                variants: array_map(function (string $uuid) {
                    return $this->variantService->findByUuid($uuid);
                }, $validatedData['product_variant']['variants']) ?? [],
                availability: [],
                data: array_merge($validatedData['product_variant'], [
                    'name' => $productVariant->name,
                    'description' => $productVariant->description,
                ])
            );
        }

        return $this->saveAndAction(
            action: $request->get('action'),
            product: $product,
        );
    }

    public function discontinue(ProductDiscontineuRequest $request): RedirectResponse
    {
        $product = $this->productService->discontinueProduct($request->get('uuid'));

        return Redirect::route('warehouse.products.show', $product);
    }

    public function continue(ProductDiscontineuRequest $request): RedirectResponse
    {
        $product = $this->productService->continueProduct($request->get('uuid'));

        return Redirect::route('warehouse.products.show', $product);
    }

    public function delete(ProductDeleteRequest $request): RedirectResponse
    {
        $this->productService->deleteProduct($request->get('uuid'));

        return Redirect::route('warehouse.products.list');
    }

    private function saveAndAction(?string $action, Product $product): RedirectResponse
    {
        if ($action) {
            $action = SaveAndAction::tryFromName($action);
        }

        return match ($action) {
            SaveAndAction::CREATE_AGAIN => Redirect::route('warehouse.products.create'),
            SaveAndAction::TO_OVERVIEW => Redirect::route('warehouse.products.list'),
            SaveAndAction::TO_MODEL => Redirect::route('warehouse.products.show', $product),
            default => null
        } ?? Redirect::route('warehouse.products.show', $product); // Redirect fallback
    }
}
