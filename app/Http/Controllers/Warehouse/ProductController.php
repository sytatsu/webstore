<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
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
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view("warehouse.product.product-create");
    }
}
