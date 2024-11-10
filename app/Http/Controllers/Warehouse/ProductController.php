<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Warehouse\ProductService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
    ) {
    }

    public function list(): Factory|View|Application
    {
        return view("warehouse.product.product-list", [
            'products' => $this->productService->getProductList(),
            'productService' => $this->productService,
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view("warehouse.product.product-create");
    }

    public function show(Product $product): Factory|View|Application
    {
        return view(view: "warehouse.product.product-show", data: [
            'product' => $this->productService->getProduct($product),
            'productService' => $this->productService,
        ]);
    }
}
