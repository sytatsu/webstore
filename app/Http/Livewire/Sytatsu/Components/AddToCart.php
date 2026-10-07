<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Lunar\Base\Purchasable;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart as LunarCart;
use Lunar\Models\ProductVariant;

class AddToCart extends Component
{
    private readonly CartService $cartService;

    private readonly BundleService $bundleService;

    /**
     * The purchasable model we want to add to the cart.
     *
     * @var ?Purchasable
     */
    public ?Purchasable $purchasable = null;

    public bool $minimalistic = false;

    /**
     * Whether the bundle-add loading overlay should visually cover the
     * *entire* surrounding tile rather than just this component's own
     * small footprint — true only when rendered from product-tile.blade.php,
     * which gives its root the `relative` positioning this overlay escapes
     * to (see that file's own comment). Left false on product.blade.php's
     * detail page, where there's no equivalent "tile" to cover and the
     * original button-only loading state already fits the layout.
     */
    public bool $coverTile = false;

    /**
     * The quantity to add to cart.
     *
     * @var int
     */
    public int $quantity = 1;

    /**
     * Set on mount whenever this purchasable's product belongs to an
     * enabled Bundle (see BundleService::findActiveBundleForProduct()).
     * While set, the normal add-to-cart UI/action is replaced entirely by
     * an "add to bundle" / "remove" control — the bundle builder is the
     * only way to acquire this product, never a standalone cart line.
     */
    public ?Bundle $activeBundle = null;

    public int $bundleQuantity = 0;

    public int $bundleAvailable = 0;

    public $listeners = [
        'cart-updated' => '$refresh',
    ];

    public function boot(CartService $cartService, BundleService $bundleService): void
    {
        $this->cartService = $cartService;
        $this->bundleService = $bundleService;
    }

    public function mount(): void
    {
        $product = $this->purchasable instanceof ProductVariant ? $this->purchasable->product : null;

        if (!$product) {
            return;
        }

        $this->activeBundle = $this->bundleService->findActiveBundleForProduct($product);

        if ($this->activeBundle) {
            $this->bundleAvailable = $this->bundleService->availableStock($this->purchasable);
            $this->bundleQuantity = $this->bundleService->getSessionSelection($this->activeBundle)[$this->purchasable->id] ?? 0;
        }
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:1|max:10000',
        ];
    }

    public function getCartProperty(): LunarCart
    {
        return $this->cartService->getCurrentCart();
    }

    public function getAvailableStockProperty(): int
    {
        return $this->cartService->getAvailableStockProperty($this->purchasable);
    }

    public function updatedQuantity($quantity): int
    {
        if (!is_int($quantity)) {
            return $this->quantity = 1;
        }

        if ($this->purchasable->purchasable === 'in_stock' && $quantity >= $this->availableStock) {
            return $this->quantity = $this->availableStock;
        }

        if ($quantity <= 1) {
            return $this->quantity = 1;
        }

        return $quantity;
    }

    public function increment(): void
    {
        $this->quantity++;
        $this->updatedQuantity($this->quantity);
    }

    public function decrement(): void
    {
        $this->quantity--;
        $this->updatedQuantity($this->quantity);
    }

    public function addToCart(): void
    {
        // Defence in depth: the Blade only ever shows this action when
        // activeBundle is null, but the Livewire action itself must also
        // refuse — a bundle-locked product must never become a standalone
        // cart line, however the request got here.
        if ($this->activeBundle) {
            return;
        }

        $this->validate();

        if ($this->purchasable->purchasable === 'in_stock' && $this->purchasable->stock < $this->quantity) {
            $this->addError('quantity', 'The quantity exceeds the available stock.');
            return;
        }

        $this->cartService->addLine($this->purchasable, $this->quantity);
        $this->dispatch('cart-updated');
        $this->dispatch('add-to-cart');
    }

    /**
     * Picks up changes made elsewhere on the same page (the tray's own
     * remove button, or another AddToCart instance for this same
     * product) — cross-page sync is the session, this is just same-page.
     */
    #[On('bundle-selection-updated')]
    public function syncBundleSelection(array $selection): void
    {
        if ($this->activeBundle) {
            $this->bundleQuantity = $selection[$this->purchasable->id] ?? 0;
        }
    }

    public function addToBundle(): void
    {
        if (!$this->activeBundle || $this->bundleQuantity >= $this->bundleAvailable) {
            return;
        }

        $this->bundleQuantity++;
        $this->bundleService->updateSessionSelectionItem($this->activeBundle, $this->purchasable->id, $this->bundleQuantity);
        $this->dispatch('bundle-item-picked', variantId: $this->purchasable->id, quantity: $this->bundleQuantity);

        // A separate, payload-less event from `bundle-item-picked` above —
        // that one also fires on every decrement/removal (same method
        // handles all three), and the tray's own pulse should only play
        // when something was actually added, not taken out.
        $this->dispatch('bundle-item-added');
    }

    public function removeFromBundleOne(): void
    {
        if (!$this->activeBundle || $this->bundleQuantity <= 0) {
            return;
        }

        $this->bundleQuantity--;
        $this->bundleService->updateSessionSelectionItem($this->activeBundle, $this->purchasable->id, $this->bundleQuantity);
        $this->dispatch('bundle-item-picked', variantId: $this->purchasable->id, quantity: $this->bundleQuantity);
    }

    public function removeFromBundle(): void
    {
        if (!$this->activeBundle) {
            return;
        }

        $this->bundleQuantity = 0;
        $this->bundleService->updateSessionSelectionItem($this->activeBundle, $this->purchasable->id, 0);
        $this->dispatch('bundle-item-picked', variantId: $this->purchasable->id, quantity: 0);
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.add-to-cart');
    }
}
