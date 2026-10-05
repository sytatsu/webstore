<?php

namespace App\Http\Livewire\Sytatsu\Components\Bundle;

use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Lunar\DataTypes\Price as PriceDataType;
use Lunar\Models\Price;
use Lunar\Models\ProductVariant;

/**
 * Page-scoped bundle picker for a collection. Owns the live selection
 * (product_variant_id => quantity), the sticky summary tray, and
 * add-to-cart / edit submission. Individual tiles never talk to this
 * directly — they dispatch `bundle-item-picked` (see
 * App\Http\Livewire\Sytatsu\Components\Bundle\BundlePickControl), and this
 * broadcasts `bundle-selection-updated` back out so every tile's own
 * control stays in sync (including when the tray removes an item, or when
 * an edit seeds the initial selection).
 *
 * See docs/bundles/README.md for the overall design.
 */
class BundleBuilder extends Component
{
    private BundleService $bundleService;

    private CartService $cartService;

    public Bundle $bundle;

    /** @var array<int, int> product_variant_id => quantity */
    public array $selection = [];

    public ?int $editingLineId = null;

    public array $bundleErrors = [];

    public bool $added = false;

    // Captured separately from `editingLineId` because that's reset to
    // null on success (so the next add starts fresh) before the view
    // renders the "added"/"updated" message.
    public bool $addedWasEdit = false;

    public function boot(BundleService $bundleService, CartService $cartService): void
    {
        $this->bundleService = $bundleService;
        $this->cartService = $cartService;
    }

    public function mount(Bundle $bundle, ?int $editLineId = null): void
    {
        $this->bundle = $bundle;
        $this->selection = $this->bundleService->selectionForCartLine($editLineId, $bundle);
        $this->editingLineId = $this->selection ? $editLineId : null;
    }

    #[On('bundle-item-picked')]
    public function updateSelection(int $variantId, int $quantity): void
    {
        if ($quantity <= 0) {
            unset($this->selection[$variantId]);
        } else {
            $this->selection[$variantId] = $quantity;
        }

        $this->added = false;
        $this->addedWasEdit = false;
        $this->dispatch('bundle-selection-updated', selection: $this->selection);
    }

    public function removeItem(int $variantId): void
    {
        $this->updateSelection($variantId, 0);
    }

    public function getTotalQuantityProperty(): int
    {
        return array_sum($this->selection);
    }

    public function getCurrentTierProperty(): ?Price
    {
        return $this->bundle->priceForQuantity($this->totalQuantity);
    }

    public function getNextTierProperty(): ?Price
    {
        return $this->bundle->nextTier($this->totalQuantity);
    }

    public function getSelectedItemsProperty(): \Illuminate\Support\Collection
    {
        if (empty($this->selection)) {
            return collect();
        }

        $variants = ProductVariant::with('product')
            ->whereIn('id', array_keys($this->selection))
            ->get()
            ->keyBy('id');

        return collect($this->selection)->map(function (int $qty, int $variantId) use ($variants) {
            $variant = $variants->get($variantId);

            if (!$variant) {
                return null;
            }

            return [
                'variant_id' => $variantId,
                'name' => $variant->product?->translateAttribute('name') ?? $variant->sku,
                'thumbnail' => $variant->getThumbnail()?->getUrl('small'),
                'quantity' => $qty,
            ];
        })->filter()->values();
    }

    public function formatPrice(int $minorUnits): string
    {
        $cart = $this->cartService->getCurrentCart();
        $currency = $cart->currency ?? \Lunar\Models\Currency::getDefault();

        return (new PriceDataType($minorUnits, $currency, 1))->formatted();
    }

    public function addToCart(): void
    {
        $this->bundleErrors = [];

        if ($this->totalQuantity < 1) {
            $this->bundleErrors[] = __('Pick at least one item for your bundle.');

            return;
        }

        $result = $this->editingLineId
            ? $this->bundleService->updateCartLine($this->editingLineId, $this->bundle, $this->selection)
            : $this->bundleService->addToCart($this->bundle, $this->selection);

        $this->bundleErrors = $result['errors'];

        // `added` (not an empty error list) is what decides success here —
        // a clamped-but-still-valid selection reports a warning in `errors`
        // even though the cart line was created; see BundleService::addToCart().
        if ($result['added']) {
            $wasEditing = (bool) $this->editingLineId;

            $this->selection = [];
            $this->editingLineId = null;
            $this->added = true;
            $this->addedWasEdit = $wasEditing;

            $this->dispatch('bundle-selection-updated', selection: $this->selection);
            $this->dispatch('cart-updated');
            $this->dispatch('add-to-cart');
            $this->dispatch($wasEditing ? 'bundle-updated' : 'bundle-added');
        }
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.bundle.bundle-builder');
    }
}
