<?php

namespace App\Http\Controllers\Catalog;

use App\Enums\Actions\SaveAndAction;
use App\Enums\ProductVariantType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\Product\ProductDeleteRequest;
use App\Http\Requests\Catalog\Product\ProductDiscontineuRequest;
use App\Http\Requests\Catalog\Product\ProductStoreRequest;
use App\Http\Requests\Catalog\Product\ProductUpdateRequest;
use App\Http\Requests\Catalog\Product\ProductVariantStoreRequest;
use App\Http\Requests\Catalog\Product\ProductVariantUpdateRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Catalog\ChannelService;
use App\Services\Catalog\BrandService;
use App\Services\Catalog\CategoryService;
use App\Services\Catalog\ProductService;
use App\Services\Catalog\VariantService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ProductVariantController extends Controller
{

    public function __construct(
        protected ProductService $productService,
        protected VariantService $variantService,
        protected ChannelService $channelService,
    ) {
        //
    }

    public function create(Product $product): View
    {
        return view("catalog.product.product-variant-create", [
            'product' => $product,
        ]);
    }

    public function store(ProductVariantStoreRequest $request): RedirectResponse
    {
        //
    }

    public function show(Product $product, ProductVariant $productVariant): View
    {
        return view("catalog.product.product-variant-show", [
            'product' => $product,
            'productVariant' => $productVariant,
        ]);
    }

    public function edit(Product $product, ProductVariant $productVariant): View
    {
        return view("catalog.product.product-variant-edit", [
            'product' => $product,
            'productVariant' => $productVariant,
        ]);
    }

    public function update(ProductVariantUpdateRequest $request, Product $product): RedirectResponse
    {
        //
    }

    public function delete(Product $product, ProductVariant $variant): RedirectResponse
    {
        //
    }


    private function saveAndAction(?string $action, Product $product, ProductVariant $productVariant): RedirectResponse
    {
        if ($action) {
            $action = SaveAndAction::tryFromName($action);
        }

        return match ($action) {
            SaveAndAction::CREATE_AGAIN => Redirect::route('catalog.products.variants.create'),
            SaveAndAction::TO_OVERVIEW => Redirect::route('catalog.products.show', $product),
            SaveAndAction::TO_MODEL => Redirect::route('catalog.products.variants.show', $product, $productVariant),
            default => null
        } ?? Redirect::route('catalog.products.variants.show', $product, $productVariant);// Redirect fallback
    }
}
