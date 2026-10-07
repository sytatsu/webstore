<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bundle;
use Illuminate\Support\Collection;
use Lunar\Models\Channel;
use Lunar\Models\Collection as LunarCollection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;

/**
 * Everything that makes a Bundle usable: finding which bundle (if any)
 * applies to a collection, which products are eligible for it, keeping its
 * hidden pricing variant in sync with the admin's tiers, validating real
 * stock, and adding/editing the single cart line a bundle becomes.
 *
 * See docs/bundles/README.md for the overall design this implements.
 */
readonly class BundleService
{
    public function __construct(
        private CartService $cartService,
        private StorefrontService $storefrontService,
    ) {
    }

    /**
     * The nearest enabled bundle for a collection: itself, or the nearest
     * ancestor. This is what lets a sub-collection page (e.g. "Safari")
     * surface the bundle configured on its parent ("Mini-friends").
     */
    public function findActiveForCollection(LunarCollection $collection): ?Bundle
    {
        $collectionIds = $collection->ancestors->pluck('id')
            ->push($collection->id)
            ->all();

        return Bundle::enabled()
            ->whereIn('collection_id', $collectionIds)
            ->with('variant.prices')
            ->get()
            ->sortByDesc(fn (Bundle $bundle) => $bundle->collection_id === $collection->id ? 1 : 0)
            ->first();
    }

    /**
     * The active bundle (if any) that locks this product out of being
     * added to the cart on its own — found by walking every collection the
     * product belongs to, and each of those collections' own ancestors.
     * Used by AddToCart to decide whether to show its normal "add to
     * cart" controls or the "add to bundle" / "remove" ones instead.
     */
    public function findActiveBundleForProduct(Product $product): ?Bundle
    {
        $collectionIds = $product->collections
            ->flatMap(fn (LunarCollection $collection) => $collection->ancestors->pluck('id')->push($collection->id))
            ->unique()
            ->values();

        if ($collectionIds->isEmpty()) {
            return null;
        }

        return Bundle::enabled()
            ->whereIn('collection_id', $collectionIds)
            ->with('variant.prices')
            ->first();
    }

    /**
     * The in-progress selection for a bundle that hasn't been added to the
     * cart yet, kept in the session (not a Livewire component's own
     * state) specifically so it survives across separate page loads —
     * the collection page's picker, a product's own detail page, and the
     * always-visible BundleBuilder tray on either all need to agree on
     * the same "what's queued right now" without a shared component.
     *
     * @return array<int, int> product_variant_id => quantity
     */
    public function getSessionSelection(Bundle $bundle): array
    {
        return session()->get($this->sessionKey($bundle), []);
    }

    /**
     * @param array<int, int> $selection product_variant_id => quantity
     */
    public function setSessionSelection(Bundle $bundle, array $selection): void
    {
        session()->put($this->sessionKey($bundle), array_filter($selection, fn (int $qty) => $qty > 0));
    }

    /**
     * Adds/updates/removes (quantity <= 0) one item in the session
     * selection and returns the resulting selection.
     *
     * @return array<int, int>
     */
    public function updateSessionSelectionItem(Bundle $bundle, int $variantId, int $quantity): array
    {
        $selection = $this->getSessionSelection($bundle);

        if ($quantity <= 0) {
            unset($selection[$variantId]);
        } else {
            $selection[$variantId] = $quantity;
        }

        $this->setSessionSelection($bundle, $selection);

        return $selection;
    }

    private function sessionKey(Bundle $bundle): string
    {
        return "bundle_selection_{$bundle->id}";
    }

    /**
     * Products a bundle can be built from: everything in the bundle's own
     * collection and all of its descendants (so picking can mix items from
     * any sub-collection), published, with at least one variant that can
     * actually be bought right now.
     *
     * @return Collection<int, Product>
     */
    public function eligibleProducts(Bundle $bundle): Collection
    {
        if (!$bundle->collection) {
            return collect();
        }

        $collectionIds = $this->storefrontService
            ->getCollectionAndDescendants($bundle->collection)
            ->pluck('id');

        return Product::query()
            ->where('status', 'published')
            ->whereHas('collections', fn ($q) => $q->whereIn('lunar_collections.id', $collectionIds))
            ->whereHas('variants', fn ($q) => $q->where(function ($q) {
                $q->where('purchasable', 'always')
                    ->orWhere('purchasable', 'backorder')
                    ->orWhere(fn ($q) => $q->where('purchasable', 'in_stock')->where('stock', '>', 0));
            }))
            ->with(['variants.prices', 'thumbnail'])
            ->get();
    }

    /**
     * How many more of this variant a customer could add right now,
     * mirroring CartService::getAvailableStockProperty()'s cart-aware
     * stock check. 'always'/'backorder' variants are treated as unlimited.
     */
    public function availableStock(ProductVariant $variant): int
    {
        if ($variant->purchasable !== 'in_stock') {
            return PHP_INT_MAX;
        }

        return max(0, $this->cartService->getAvailableStockProperty($variant));
    }

    /**
     * The selection a bundle cart line currently represents, keyed by
     * product_variant_id => quantity — used to seed both the BundleBuilder
     * tray and every tile's pick control when editing an existing line.
     * Returns an empty array when the line doesn't exist, isn't a bundle
     * line, or belongs to a different bundle.
     *
     * @return array<int, int>
     */
    public function selectionForCartLine(?int $lineId, Bundle $bundle): array
    {
        if (!$lineId) {
            return [];
        }

        $line = collect($this->cartService->mapCartLines())->firstWhere('id', $lineId);
        $bundleMeta = $line['meta']['bundle'] ?? null;

        if (!$bundleMeta || (int) $bundleMeta['bundle_id'] !== $bundle->id) {
            return [];
        }

        return collect($bundleMeta['items'])
            ->mapWithKeys(fn (array $item) => [$item['product_variant_id'] => $item['quantity']])
            ->all();
    }

    /**
     * Re-checks every picked item against real, current stock. Returns the
     * selection with any over-picked quantities clamped down, plus a list
     * of human-readable problems (empty when nothing had to change).
     *
     * @param array<int, int> $selection product_variant_id => quantity
     * @return array{selection: array<int, int>, errors: array<int, string>}
     */
    public function validateSelection(Bundle $bundle, array $selection): array
    {
        $errors = [];
        $clamped = [];

        foreach ($selection as $variantId => $quantity) {
            $variant = ProductVariant::find($variantId);

            if (!$variant || $quantity < 1) {
                continue;
            }

            $available = $this->availableStock($variant);

            if ($available <= 0) {
                $errors[] = __(':product is out of stock.', ['product' => $variant->product?->translateAttribute('name') ?? $variant->sku]);
                continue;
            }

            if ($quantity > $available) {
                $errors[] = __('Only :available of :product left — the quantity has been adjusted.', [
                    'available' => $available,
                    'product' => $variant->product?->translateAttribute('name') ?? $variant->sku,
                ]);
                $quantity = $available;
            }

            $clamped[$variantId] = $quantity;
        }

        if ($bundle->max_items && array_sum($clamped) > $bundle->max_items) {
            $errors[] = __('A bundle can contain at most :max items.', ['max' => $bundle->max_items]);
        }

        return ['selection' => $clamped, 'errors' => $errors];
    }

    /**
     * Builds the traceable `meta['bundle']` payload for a selection.
     *
     * @param array<int, int> $selection product_variant_id => quantity
     */
    public function buildMeta(Bundle $bundle, array $selection): array
    {
        $variants = ProductVariant::with('product')
            ->whereIn('id', array_keys($selection))
            ->get()
            ->keyBy('id');

        $quantity = array_sum($selection);
        $tier = $bundle->priceForQuantity($quantity);

        $items = collect($selection)->map(function (int $qty, int $variantId) use ($variants) {
            $variant = $variants->get($variantId);

            if (!$variant) {
                return null;
            }

            return [
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'name' => $variant->product?->translateAttribute('name') ?? $variant->sku,
                'thumbnail' => $variant->getThumbnail()?->getUrl('small'),
                'quantity' => $qty,
            ];
        })->filter()->values()->all();

        return [
            'bundle_id' => $bundle->id,
            'collection_id' => $bundle->collection_id,
            'name' => $bundle->getTranslatedName(),
            'tier' => $tier ? ['min_quantity' => $tier->min_quantity, 'price' => $tier->price->value] : null,
            'items' => $items,
        ];
    }

    /**
     * Validates the selection, then adds it as one cart line against the
     * bundle's hidden pricing variant.
     *
     * @param array<int, int> $selection product_variant_id => quantity
     * @return array{errors: array<int, string>, added: bool} `added` tells
     *     the caller whether a cart line actually resulted — `errors` can
     *     be non-empty on a successful add too (stock got clamped but
     *     something valid remained), so callers must check `added`, not
     *     `empty(errors)`, to decide whether to treat this as a success.
     */
    public function addToCart(Bundle $bundle, array $selection): array
    {
        $result = $this->validateSelection($bundle, $selection);
        $selection = $result['selection'];

        if (!$bundle->variant || array_sum($selection) < 1) {
            return ['errors' => $result['errors'], 'added' => false];
        }

        $this->cartService->addLine($bundle->variant, array_sum($selection), [
            'bundle' => $this->buildMeta($bundle, $selection),
        ]);

        return ['errors' => $result['errors'], 'added' => true];
    }

    /**
     * Edits an existing bundle cart line. There's no existing precedent in
     * this codebase for an in-place meta+quantity patch on a cart line (see
     * docs/bundles/README.md), so this takes the simplest correct route:
     * remove the old line, then add the edited selection fresh.
     *
     * @param array<int, int> $selection product_variant_id => quantity
     * @return array{errors: array<int, string>, added: bool}
     */
    public function updateCartLine(int $lineId, Bundle $bundle, array $selection): array
    {
        $result = $this->addToCart($bundle, $selection);

        if ($result['added']) {
            $this->cartService->removeLine($lineId);
        }

        return $result;
    }

    /**
     * Creates the hidden product/variant on first save, then replaces its
     * Price rows with the admin's tiers (delete-and-recreate — the Price
     * rows ARE the tier configuration, there's no separate JSON copy of it).
     *
     * @param array<int, array{min_quantity: int, price: float}> $tiers
     */
    public function syncPurchasable(Bundle $bundle, array $tiers): void
    {
        // $bundle->variant may already have been accessed (and cached as
        // null) before this call, e.g. by whatever loaded the record for
        // editing — so provisioning must explicitly refresh that relation
        // cache, or a stale null sticks around for the rest of the request.
        $variant = $bundle->variant ?: $this->provisionVariant($bundle);
        $bundle->setRelation('variant', $variant);

        $defaultCurrency = Currency::whereDefault(true)->first() ?? Currency::first();

        $variant->prices()->where('customer_group_id', null)->delete();

        foreach ($tiers as $tier) {
            $variant->prices()->create([
                'currency_id' => $defaultCurrency->id,
                'customer_group_id' => null,
                'min_quantity' => (int) $tier['min_quantity'],
                'price' => (int) round(((float) $tier['price']) * (10 ** $defaultCurrency->decimal_places)),
            ]);
        }

        $variant->load('prices');
    }

    private function provisionVariant(Bundle $bundle): ProductVariant
    {
        $productType = ProductType::first();
        $defaultChannel = Channel::whereDefault(true)->first() ?? Channel::first();
        $defaultCustomerGroup = CustomerGroup::whereDefault(true)->first() ?? CustomerGroup::first();
        $defaultTaxClass = TaxClass::whereDefault(true)->first() ?? TaxClass::first();

        $product = Product::create([
            'product_type_id' => $productType->id,
            // Hidden on purpose, but status MUST stay 'published': any
            // non-admin, non-Livewire request (a plain cart/checkout page
            // render, this very test suite) is NOT exempted by
            // App\Scopes\PublishedProductScope, so a 'draft' status here
            // would make `$variant->product` resolve to null mid-checkout
            // and crash (ProductVariant::getThumbnail() dereferences it
            // unconditionally). It's still never found through normal
            // browsing/search because it's never attached to any
            // collection and carries no URL.
            'status' => 'published',
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Bundle: ' . ($bundle->getTranslatedName() ?: $bundle->collection?->translateAttribute('name'))),
            ]),
        ]);

        if ($defaultChannel) {
            $product->channels()->attach($defaultChannel->id, ['enabled' => true]);
        }

        if ($defaultCustomerGroup) {
            $product->customerGroups()->attach($defaultCustomerGroup->id, [
                'enabled' => true,
                'visible' => true,
                'purchasable' => true,
            ]);
        }

        $variant = $product->variants()->create([
            'sku' => 'BUNDLE-' . $bundle->collection_id . '-' . now()->timestamp,
            'tax_class_id' => $defaultTaxClass?->id,
            'shippable' => true,
            'purchasable' => 'always',
            'unit_quantity' => 1,
        ]);

        $bundle->forceFill(['product_variant_id' => $variant->id])->save();

        return $variant;
    }
}
