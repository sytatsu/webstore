<?php

namespace App\Http\Livewire\Sytatsu\Components\Product;

use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\WebstoreHelperService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Models\Product;

class ProductTile extends Component
{
    public Product $product;

    public ?Bundle $activeBundle = null;

    public function mount(Product $product, BundleService $bundleService): void
    {
        $this->product = $product;
        // Its own price is meaningless here — the bundle, not this product,
        // decides what it costs. See AddToCart::mount() for the same check.
        $this->activeBundle = $bundleService->findActiveBundleForProduct($product);
    }

    public function getPriceRangeString(): string
    {
        return WebstoreHelperService::priceRangeString(priceCollection: $this->product->prices);
    }

    public function getProductOptionsArray(): array
    {
        return WebstoreHelperService::productOptionsArray(product: $this->product);
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.livewire.product.product-tile', [
            'product' => $this->product
        ]);
    }
}
