<?php

namespace App\Http\Livewire\Sytatsu\Components\Bundle;

use App\Models\BundleConfig;
use App\Services\BundleSessionService;
use App\Services\CartService;
use Livewire\Component;
use Lunar\Models\Collection;
use Lunar\Models\ProductVariant;

class BundlePanel extends Component
{
    public Collection $collection;

    public array $selectedItems = [];
    public ?string $activeBundleId = null;

    protected $listeners = ['bundle-item-toggled' => 'handleItemToggled'];

    public function mount(Collection $collection): void
    {
        $this->collection = $collection;

        $bundleService = app(BundleSessionService::class);
        if ($bundleService->getCollectionId() === $collection->id) {
            $this->activeBundleId = $bundleService->getBundleId();
        }
    }

    private function bundleConfig(): ?BundleConfig
    {
        return BundleConfig::where('collection_id', $this->collection->id)
            ->where('enabled', true)
            ->first();
    }

    public function handleItemToggled(array $data): void
    {
        $variantId = (string) $data['variantId'];
        $quantity = (int) $data['quantity'];

        if ($quantity <= 0) {
            unset($this->selectedItems[$variantId]);
        } else {
            $this->selectedItems[$variantId] = [
                'name' => $data['productName'],
                'thumbnail' => $data['thumbnailUrl'],
                'priceValue' => (int) $data['priceValue'],
                'quantity' => $quantity,
            ];
        }

        $this->dispatch('bundle-panel-item-updated', variantId: (int) $variantId, quantity: $quantity);
    }

    public function removeItem(string $variantId): void
    {
        if (isset($this->selectedItems[$variantId])) {
            $variantIdInt = (int) $variantId;
            unset($this->selectedItems[$variantId]);
            $this->dispatch('bundle-item-removed', variantId: $variantIdInt);
        }
    }

    public function getSelectedCountProperty(): int
    {
        return array_sum(array_column($this->selectedItems, 'quantity'));
    }

    public function getRawTotalProperty(): int
    {
        return array_sum(array_map(
            fn(array $item) => $item['priceValue'] * $item['quantity'],
            $this->selectedItems
        ));
    }

    public function getActiveTierProperty(): ?array
    {
        return $this->bundleConfig()?->getActiveTier($this->selectedCount);
    }

    public function getDiscountPctProperty(): float
    {
        return (float) ($this->activeTier['discount_pct'] ?? 0);
    }

    public function getDiscountedTotalProperty(): int
    {
        if ($this->discountPct <= 0) {
            return $this->rawTotal;
        }

        return (int) round($this->rawTotal * (1 - $this->discountPct / 100));
    }

    public function getNextTierProperty(): ?array
    {
        return $this->bundleConfig()?->getNextTier($this->selectedCount);
    }

    public function getBundleNameProperty(): string
    {
        return $this->bundleConfig()?->getTranslatedName() ?? '';
    }

    public function addToCart(): void
    {
        if ($this->selectedCount < 1) {
            return;
        }

        $bundleService = app(BundleSessionService::class);
        $cartService = app(CartService::class);

        $bundleConfigId = $this->bundleConfig()?->id;
        $bundleName = $this->bundleName;

        $this->activeBundleId = $bundleService->startNewBundle($this->collection->id, $bundleName);
        $bundleService->setDiscount($this->discountPct);

        foreach ($this->selectedItems as $variantId => $item) {
            $variant = ProductVariant::find((int) $variantId);
            if ($variant) {
                $cartService->addBundleLine($variant, $item['quantity'], $this->activeBundleId, $this->discountPct, $bundleConfigId, $bundleName);
            }
        }

        $this->dispatch('cart-updated');
        $this->dispatch('add-to-cart');
        $this->dispatch('bundle-added-success');

        // Automatically start a new bundle
        $this->clear();
    }

    public function startNewBundle(): void
    {
        $bundleService = app(BundleSessionService::class);
        $this->activeBundleId = $bundleService->startNewBundle($this->collection->id, $this->bundleName);
        $this->clear();
    }

    public function clear(): void
    {
        $this->selectedItems = [];
        $this->dispatch('bundle-reset-selection');
    }

    public function getFormattedPrice(int $value): string
    {
        $cart = \Lunar\Facades\CartSession::current();
        if (!$cart) {
            return number_format($value / 100, 2);
        }
        $price = new \Lunar\DataTypes\Price($value, $cart->currency, 1);
        return $price->formatted();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('sytatsu.components.livewire.bundle.bundle-panel');
    }
}
