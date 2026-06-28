<?php

namespace App\Http\Livewire\Sytatsu\Components\Bundle;

use App\Services\CartService;
use App\Services\StorefrontService;
use Livewire\Component;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class BundleProductModal extends Component
{
    public ?Product $product = null;
    public array $selectedOptionValues = [];
    public int $quantity = 0;
    public bool $isOpen = false;

    protected $listeners = [
        'open-bundle-product-modal' => 'open',
        'bundle-reset-selection' => 'resetModal',
        'bundle-item-removed' => 'handleItemRemoved',
    ];

    public function updatedSelectedOptionValues(): void
    {
        $this->quantity = min($this->quantity, $this->availableStock ?? $this->quantity);
        if ($this->quantity < 1 && ($this->availableStock ?? 0) > 0) {
            $this->quantity = 1;
        }
    }

    public function increment(): void
    {
        if ($this->quantity < $this->availableStock) {
            $this->quantity++;
        }
    }

    public function decrement(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function handleItemRemoved(int $variantId): void
    {
        if ($this->isOpen && $this->variant?->id === $variantId) {
            $this->close();
        }
    }

    public function resetModal(): void
    {
        if ($this->isOpen) {
            $this->close();
        }
    }

    public function open(int $productId, int $quantity = 0): void
    {
        $this->product = Product::with(['variants.images', 'variants.prices', 'images', 'options.values'])->find($productId);

        if (!$this->product) {
            return;
        }

        $storefrontService = app(StorefrontService::class);
        $this->selectedOptionValues = $storefrontService->getDefaultSelectedOptions($this->product);

        $availableStock = $this->availableStock;
        if ($availableStock !== null && $availableStock <= 0) {
            $this->quantity = 0;
        } else {
            $this->quantity = $quantity > 0 ? $quantity : 1;
            if ($availableStock !== null) {
                $this->quantity = min($this->quantity, $availableStock);
            }
        }

        $this->isOpen = true;
    }

    public function getAvailableStockProperty(): ?int
    {
        $variant = $this->variant;
        if (!$variant || $variant->purchasable !== 'in_stock') {
            return null;
        }

        return app(CartService::class)->getAvailableStockProperty($variant, 0);
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->product = null;
        $this->selectedOptionValues = [];
        $this->quantity = 0;
    }

    public function setSelectedOptionValue(int $optionId, int $valueId): void
    {
        $this->selectedOptionValues[$optionId] = $valueId;
    }

    public function getVariantProperty(): ?ProductVariant
    {
        if (!$this->product) {
            return null;
        }

        $storefrontService = app(StorefrontService::class);
        $variant = $storefrontService->findVariantByOptions($this->product, $this->selectedOptionValues);

        return $variant ?: $this->product->variants->first();
    }

    public function getProductOptionsProperty(): \Illuminate\Support\Collection
    {
        if (!$this->product) {
            return collect();
        }

        $storefrontService = app(StorefrontService::class);
        return $storefrontService->getProductOptionsWithValues($this->product);
    }

    public function addToBundle(): void
    {
        $variant = $this->variant;
        if (!$variant) {
            return;
        }

        $this->dispatch('bundle-item-toggled', [
            'variantId' => $variant->id,
            'productName' => $this->product->translateAttribute('name'),
            'thumbnailUrl' => $variant->thumbnail?->getUrl() ?? $this->product->thumbnail?->getUrl(),
            'priceValue' => $variant->prices->first()?->price->value ?? 0,
            'quantity' => $this->quantity,
        ]);

        $this->close();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('sytatsu.components.livewire.bundle.bundle-product-modal');
    }
}
