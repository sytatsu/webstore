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
 * Always-visible bundle tray — mounted on every page that's relevant to a
 * bundle (the collection page, and any eligible product's own detail
 * page), pinned to the bottom of the screen. Its selection
 * (product_variant_id => quantity) is NOT private component state: it's
 * read from and written straight through to
 * App\Services\BundleService::getSessionSelection()/updateSessionSelectionItem(),
 * because this component and every `AddToCart` instance on a completely
 * different page load all need to agree on "what's queued right now"
 * without a shared Livewire component to hold it. `AddToCart` dispatches
 * `bundle-item-picked` (handled here) when its own page happens to have a
 * tray mounted too, and this broadcasts `bundle-selection-updated` back
 * out so every other bundle-aware control on the SAME page stays in sync
 * (e.g. the tray's own remove button, or another AddToCart instance for
 * the same product). Cross-page sync is the session, not these events.
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

        $editSelection = $this->bundleService->selectionForCartLine($editLineId, $bundle);

        if ($editSelection) {
            // Editing an existing line always wins over whatever happened
            // to already be queued in the session, and becomes the new
            // session state so every other control agrees with it too.
            $this->bundleService->setSessionSelection($bundle, $editSelection);
            $this->editingLineId = $editLineId;
        }

        $this->selection = $this->bundleService->getSessionSelection($bundle);
    }

    #[On('bundle-item-picked')]
    public function updateSelection(int $variantId, int $quantity): void
    {
        $this->selection = $this->bundleService->updateSessionSelectionItem($this->bundle, $variantId, $quantity);

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
            $this->bundleService->setSessionSelection($this->bundle, []);

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
