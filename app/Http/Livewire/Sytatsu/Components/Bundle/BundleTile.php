<?php

namespace App\Http\Livewire\Sytatsu\Components\Bundle;

use App\Services\CartService;
use App\Services\WebstoreHelperService;
use Livewire\Component;
use Lunar\Models\Product;

class BundleTile extends Component
{
    public Product $product;
    public int $quantity = 0;

    protected $listeners = [
        'bundle-reset-selection' => 'reset',
        'bundle-item-removed' => 'handleItemRemoved',
        'bundle-panel-item-updated' => 'handlePanelItemUpdated',
    ];

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function handlePanelItemUpdated(int $variantId, int $quantity): void
    {
        if ($this->product->variants->pluck('id')->contains($variantId)) {
            $this->quantity = $quantity;
        }
    }

    public function handleItemRemoved(int $variantId): void
    {
        if ($this->product->variants->pluck('id')->contains($variantId)) {
            $this->quantity = 0;
        }
    }

    public function increment(): void
    {
        if ($this->product->variants->count() > 1) {
            $this->dispatch('open-bundle-product-modal', productId: $this->product->id, quantity: $this->quantity);
            return;
        }

        $variant = $this->product->variants->first();
        if ($variant->purchasable === 'in_stock') {
            $inBundleQty = $this->quantity;
            $availableStock = app(CartService::class)->getAvailableStockProperty($variant, $inBundleQty);
            if ($availableStock <= 0) {
                return;
            }
        }

        $this->quantity++;
        $this->dispatchToggled();
    }

    public function decrement(): void
    {
        if ($this->quantity <= 0) {
            return;
        }

        $this->quantity--;
        $this->dispatchToggled();
    }

    private function dispatchToggled(): void
    {
        $variant = $this->product->variant;

        $this->dispatch('bundle-item-toggled', [
            'variantId' => $variant->id,
            'productName' => $this->product->translateAttribute('name'),
            'thumbnailUrl' => $this->product->thumbnail?->getUrl(),
            'priceValue' => $variant->prices->first()?->price->value ?? 0,
            'quantity' => $this->quantity,
        ]);
    }

    public function reset(...$properties): void
    {
        $this->quantity = 0;
    }

    public function getPriceString(): string
    {
        return WebstoreHelperService::priceRangeString($this->product->prices);
    }

    public function getAvailableStockProperty(): ?int
    {
        if ($this->product->variants->count() > 1) {
            return null;
        }

        $variant = $this->product->variants->first();
        if ($variant->purchasable !== 'in_stock') {
            return null;
        }

        return app(CartService::class)->getAvailableStockProperty($variant, 0);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('sytatsu.components.livewire.bundle.bundle-tile');
    }
}
