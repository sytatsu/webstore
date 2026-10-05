<?php

namespace App\Http\Livewire\Sytatsu\Components\Bundle;

use App\Services\BundleService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

/**
 * The small "+ / qty / -" overlay rendered on top of an (otherwise
 * untouched) `product-tile` when a collection's bundle picker is active.
 * One instance per eligible product. It never touches the cart directly —
 * it only tells the page's single `BundleBuilder` what changed, and stays
 * in sync with it via the `bundle-selection-updated` broadcast (so e.g.
 * removing an item from the summary tray, or seeding an edit, is reflected
 * back on the tile).
 */
class BundlePickControl extends Component
{
    private BundleService $bundleService;

    public Product $product;

    public ?ProductVariant $variant = null;

    public int $quantity = 0;

    public int $available = 0;

    public function boot(BundleService $bundleService): void
    {
        $this->bundleService = $bundleService;
    }

    public function mount(Product $product, array $initialSelection = []): void
    {
        $this->product = $product;
        $this->variant = $this->bundleService->pickableVariant($product);

        if ($this->variant) {
            $this->available = $this->bundleService->availableStock($this->variant);
            $this->quantity = $initialSelection[$this->variant->id] ?? 0;
        }
    }

    #[On('bundle-selection-updated')]
    public function syncFromSelection(array $selection): void
    {
        if ($this->variant) {
            $this->quantity = $selection[$this->variant->id] ?? 0;
        }
    }

    public function increment(): void
    {
        if (!$this->variant || $this->quantity >= $this->available) {
            return;
        }

        $this->quantity++;
        $this->emitPick();
    }

    public function decrement(): void
    {
        if (!$this->variant || $this->quantity <= 0) {
            return;
        }

        $this->quantity--;
        $this->emitPick();
    }

    private function emitPick(): void
    {
        $this->dispatch('bundle-item-picked', variantId: $this->variant->id, quantity: $this->quantity);
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.bundle.bundle-pick-control');
    }
}
