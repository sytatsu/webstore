<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\CartService;
use App\Services\StorefrontService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\DataTypes\Price as PriceDataType;
use Lunar\Models\Currency;
use Lunar\Models\Product;

class SearchBox extends Component
{
    public string $query = '';

    private const MIN_QUERY_LENGTH = 2;

    private BundleService $bundleService;

    private CartService $cartService;

    public function boot(BundleService $bundleService, CartService $cartService): void
    {
        $this->bundleService = $bundleService;
        $this->cartService = $cartService;
    }

    /**
     * This result row has no AddToCart/ProductTile of its own (it's a
     * compact list, not a grid tile), so it needs its own bundle check —
     * same one AddToCart::mount() and ProductTile::mount() already do —
     * to show the bundle's own price instead of the product's normal
     * one, which is meaningless once a bundle is the only way to buy it.
     */
    public function activeBundleFor(Product $product): ?Bundle
    {
        return $this->bundleService->findActiveBundleForProduct($product);
    }

    public function formatPrice(int $minorUnits): string
    {
        $cart = $this->cartService->getCurrentCart();
        $currency = $cart->currency ?? Currency::getDefault();

        return (new PriceDataType($minorUnits, $currency, 1))->formatted();
    }

    public function getResultsProperty(): Collection
    {
        $term = trim($this->query);

        if (mb_strlen($term) < self::MIN_QUERY_LENGTH) {
            return collect();
        }

        return app(StorefrontService::class)->searchProducts($term, 6);
    }

    public function getPagesProperty(): Collection
    {
        $term = trim($this->query);

        if (mb_strlen($term) < self::MIN_QUERY_LENGTH) {
            return collect();
        }

        return app(StorefrontService::class)->searchPages($term);
    }

    public function search(): ?Redirector
    {
        $term = trim($this->query);

        if ($term === '') {
            return null;
        }

        return redirect()->route('sytatsu.webstore.search', ['q' => $term]);
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.livewire.search-box', [
            'results' => $this->getResultsProperty(),
            'pages' => $this->getPagesProperty(),
        ]);
    }
}
