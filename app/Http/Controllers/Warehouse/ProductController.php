<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\Actions\SaveAndAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\Product\ProductStoreRequest;
use App\Models\Product;
use App\Services\Warehouse\BrandService;
use App\Services\Warehouse\CategoryService;
use App\Services\Warehouse\ProductService;
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

    public function show(Product $product): Factory|View|Application
    {
        return view(view: "warehouse.product.product-show", data: [
            'product' => $this->productService->getProduct($product),
            'productService' => $this->productService,
        ]);
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $product = $this->productService->storeProduct(
            product: null,
            category: $this->categoryService->findByUuid($request->get('category')),
            brand: $this->brandService->findByUuid($request->get('brand')),
            data: $request->validated()
        );

        return $this->saveAndAction(
            action: $request->get('action'),
            product: $product,
        );
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
