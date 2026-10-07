<?php

namespace App\Http\Livewire\Sytatsu\Components\Product;

use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\WebstoreHelperService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Lunar\Models\Collection;
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

    /**
     * Any one collection this product belongs to that is itself a child of
     * another (`parent_id` set) — a "sub-collection" by definition,
     * regardless of which parent collection the tile happens to be
     * rendered under. Shown on the tile (if there is one) as a small,
     * clickable label; clicking it applies that sub-collection as a
     * filter wherever CollectionFilters is on the same page — see the
     * `sub-collection-selected` event both sides agree on. Not scoped to
     * any one collection's own sub-collections, so it works the same way
     * regardless of which collection page the tile is listed on.
     */
    public function getSubCollectionProperty(): ?Collection
    {
        return $this->product->collections->first(fn (Collection $collection) => $collection->parent_id !== null);
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
