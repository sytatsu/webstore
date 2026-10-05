# Collection bundles ("mini-friends" bundles)

Lets an admin turn a collection (e.g. **Mini-friends**) into a "buy N, pay
less per item" bundle: customers mix-and-match any product from that
collection or its sub-collections, and the price per item drops at
quantity thresholds the admin configures.

Branch: `feature/mini-friends-bundles`. Built from scratch after a prior
attempt (`feature/bundles`, still on the remote, not merged) was shelved —
see "Why not the old `feature/bundles` branch" below before changing this
feature, so you don't repeat what didn't work.

## The core trick: a hidden product carries the pricing

Lunar already supports quantity price-breaks on any product variant: a
`Price` row with `min_quantity > 1` is automatically picked up by
`Lunar\Managers\PricingManager::get()` once a cart line's quantity reaches
it — no custom discount code needed, it's exactly how Lunar does bulk
pricing on normal products.

So each `App\Models\Bundle` (one per collection, table `bundles`) owns a
single **hidden** `Lunar\Models\Product` + one `ProductVariant`
(`purchasable = 'always'`, never attached to any collection or given a
URL). That variant gets one `Price` row per tier the admin
configures, keyed by `min_quantity`. When a customer adds a bundle to the
cart, **this hidden variant is the cart line's purchasable**, and the cart
line's quantity = the total number of real items picked. Lunar's own
pricing resolves the per-item price from that quantity automatically.

The *real* products the customer picked (figures, etc.) are never their own
cart lines — they're recorded in the cart line's `meta['bundle']`:

```php
$line->meta['bundle'] = [
    'bundle_id' => 3,
    'collection_id' => 12,
    'name' => 'Mini-friends bundle',
    'tier' => ['min_quantity' => 4, 'price' => 125], // the tier that matched, in minor units
    'items' => [
        ['product_variant_id' => 91, 'product_id' => 40, 'name' => 'Fox', 'thumbnail' => '...', 'quantity' => 2],
        ['product_variant_id' => 77, 'product_id' => 33, 'name' => 'Owl', 'thumbnail' => '...', 'quantity' => 2],
    ],
];
```

This mirrors exactly how the existing **Clickerz Bar Builder** rides
descriptive `meta['bar_builder']` on its cart line
(`App\Http\Livewire\Sytatsu\Components\BarBuilder::addToCart()`), and it's
what every traceability surface below keys off.

Status stays `published`, deliberately — `App\Scopes\PublishedProductScope`
only skips enforcement for admin/Livewire requests, so a `draft` status
here makes `$variant->product` resolve to `null` on any plain page render
(and crash inside `ProductVariant::getThumbnail()`, which dereferences it
unconditionally). It still never surfaces through normal browsing or
search, because it's never attached to a collection and has no URL.

`App\Services\BundleService` is where all of this lives:
`syncPurchasable()` keeps the hidden variant's `Price` rows in sync with
the admin's tiers (delete-and-recreate — the `Price` rows ARE the tier
config, there's no separate JSON copy), `findActiveForCollection()`,
`eligibleProducts()`, `addToCart()` / `updateCartLine()`, and the stock
checks described next.

## Stock is validated by us, not by Lunar

Lunar's own cart-line stock validation only ever looks at the *hidden*
variant, which is `purchasable = 'always'` (unlimited) on purpose — it
carries pricing, not inventory. All real stock enforcement for the actual
picked products happens in `BundleService`:

- `availableStock(ProductVariant $variant)` — cart-aware, reuses
  `CartService::getAvailableStockProperty()`'s logic.
- `validateSelection()` — re-checks every picked item's stock and clamps
  anything that's gone stale, called both before adding to the cart and
  before saving an edit (picks can go stale between page load and submit).
- The storefront picker (`BundlePickControl`) also disables "+" once a
  tile's own available stock is reached, so the common case never even
  reaches the server-side check.

If you extend this feature, **don't assume Lunar's `CartLineQuantity`
validation protects bundle stock** — it doesn't, by design.

## Editing a bundle already in the cart

There's no existing precedent in this codebase for patching an existing
cart line's `meta`/quantity in place — `CartService`'s update paths
(`updateLines`/`CartSession::manager()->add()`) aren't built for that.
`BundleService::updateCartLine()` takes the simplest correct route:
validate the new selection, add it as a fresh line, then remove the old
line. Functionally identical to an in-place edit from the customer's
perspective (same effect on the cart), just not literally the same cart
line id across the edit. If a future need (e.g. preserving a specific
line's created-at for sorting) requires a true in-place patch, that's new
ground — budget time for it.

The storefront flow: the cart's "Edit" button
(`resources/views/sytatsu/components/livewire/cart/components/items.blade.php`)
links to the bundle's own collection page with `?edit_bundle_line={id}`.
`App\Http\Livewire\Sytatsu\Components\Bundle\BundleBuilder::mount()` reads
that, seeds `$selection` from `BundleService::selectionForCartLine()`, and
`addToCart()` becomes an edit (`updateCartLine()`) for that line id.
`BundlePickControl` instances on the grid get the same seeded selection via
`$bundleEditSelection` (computed once in `CollectionPage::render()`) so the
tiles show the right starting quantities too.

## Storefront pieces (and why the collection page itself barely changed)

- `CollectionPage::mount()` resolves the active bundle via
  `BundleService::findActiveForCollection()`, which checks the collection
  **and its ancestors** — so a sub-collection page (Safari, Ocean, …) picks
  up the bundle configured on the parent (Mini-friends), and the picker's
  eligible product set is always the *whole* bundle collection's tree
  (`eligibleProducts()`), not just the sub-collection being browsed. That's
  what lets a bundle mix items across sibling sub-collections.
- `resources/views/sytatsu/webstore/collection.blade.php` gained one
  **additive** block: a toggle banner + the `BundleBuilder` tray, both
  gated behind `@if($bundle ?? null)`. The existing filters/grid/pagination
  markup is untouched — when there's no bundle for a collection, this file
  renders byte-for-byte what it did before.
- Each product tile stays the single-line `<livewire:...product-tile>` it
  always was. Only when a bundle applies AND that specific product is
  eligible does it get wrapped in a `position: relative` div with a small
  `BundlePickControl` overlay (`+`/qty/`-`) absolutely positioned on top —
  `ProductTile.php` itself is never touched.
- `BundleBuilder` (one instance, page-scoped) and `BundlePickControl` (one
  per eligible tile) talk to each other only through Livewire's
  cross-component events — `bundle-item-picked` (tile → builder) and
  `bundle-selection-updated` (builder → every tile, keeps counts in sync
  when the tray removes an item or an edit seeds the selection). Neither
  touches the other's PHP state directly.

### Why not the old `feature/bundles` branch

Reading its diff against `main` explains three concrete problems this
design deliberately avoids — worth re-reading before changing this
feature, so a future rewrite doesn't reintroduce them:

1. It replaced `collection.blade.php`'s real markup with a bare
   `@yield('content')` shell and forked the page into
   `collection-pages/default.blade.php` / `collection-pages/bundle.blade.php`
   via a new `collection_view` attribute. The "bundle" variant dropped
   filters, pagination, and sub-collection drill-down entirely.
2. Each bundle item was its own cart line, linked only by a session-held
   "active bundle id", with a flat `%` knocked off *that product's own
   price* — different products in the same bundle ended up at different
   final prices, and there was never one line to point an "edit" button at.
3. Its `OrderItemsTableExtension` swapped the `extendTable` hook for
   `extendOrderLinesTableColumns`, which silently deleted the existing
   `barBuilderPreview` admin action instead of adding alongside it.

## Admin / back-end

- `App\Filament\Resources\BundleResource` (cluster `App\Filament\Clusters\Bundles`,
  single `ManageBundles` page): pick the collection, set a translatable
  name, enable/disable, optional `max_items`, and a tiers repeater
  (`min_quantity` + `price`). One tier at `min_quantity = 1` is required —
  that's the base, undiscounted price. Saving calls
  `BundleService::syncPurchasable()`, which provisions the hidden
  product/variant on first save and replaces its `Price` rows from the
  repeater every time (via `EditAction`/`CreateAction::using()` +
  `EditAction::mutateRecordDataUsing()` to round-trip the repeater against
  real `Price` rows instead of a separate JSON copy).
- `App\Filament\Extensions\OrderItemsTableExtension::extendTable()` gained
  a **second** action, `bundleContentsPreview`, alongside the existing
  `barBuilderPreview` — both still registered on the same `extendTable`
  hook. `resources/views/filament/orders/bundle-preview.blade.php` lists
  the picked products, quantities, and matched tier.
- `resources/views/mail/sytatsu/orders/includes/order-table.blade.php`
  gained one more `@if($bundle) @include(...) @endif` row next to the
  existing `$barBuilder` one, backed by
  `resources/views/mail/sytatsu/orders/includes/bundle-details.blade.php`.

## Known v1 scope limits (left for whoever picks this up next)

- **One variant per product.** Bundle items are picked at the product
  level (`BundleService::pickableVariant()` just takes
  `$product->variants->first()`). If a bundle-eligible product ever needs
  real option variants (size/colour), the picker will need a variant
  selector, not just a product tile.
- **No live auto-hide on "Bundle added!".** The success state in
  `bundle-builder.blade.php` stays until the next pick; the old branch had
  a 3-second `setTimeout` auto-hide (Alpine `x-data` + `x-on:bundle-added-success.window`)
  that's a reasonable thing to port over if it's missed.
- **`edit_bundle_line` is a plain query string, not a Livewire `queryString`
  property** on `CollectionPage`. It's only read at the initial full-page
  load (`mount()`/the first `render()`), which is correct for the "click
  Edit in the cart" flow, but don't expect it to survive being manually
  edited mid-session via a Livewire AJAX update.

## Tests

27 tests, `tests/Feature/Bundle{Pricing,Eligibility,Cart,Admin,AdminHttp,BuilderComponent,OrderRendering}Test.php`:

- **Pricing** — tier resolution at/between thresholds, re-saving tiers
  replaces rather than duplicates `Price` rows.
- **Eligibility** — descendant sub-collection products are included,
  out-of-stock/unpublished products are excluded, a sub-collection page
  surfaces its parent's bundle, disabled bundles are never found.
- **Cart** — adding a bundle produces exactly one cart line with the
  matched tier price and a full item list in `meta`; stock is clamped and
  reported; editing a line replaces it rather than duplicating it.
- **BuilderComponent** — `BundleBuilder`/`BundlePickControl` as real
  Livewire components: the cross-component event handshake
  (`bundle-item-picked` / `bundle-selection-updated`), the pick control's
  own stock cap, and — the one bug this caught — that a stock-clamped
  selection still counts as `added` (the cart line *was* created, just
  smaller than asked), so the tray must key off the `added` flag
  `BundleService::addToCart()`/`updateCartLine()` return, never
  `empty($bundleErrors)`. Getting that backwards risks a double-add: the
  UI would look like nothing happened and let the customer submit again
  while the first (successful) line sits in the cart.
- **Admin** — the `OrderItemsTableExtension` regression test (both preview
  actions present), plus real end-to-end `Livewire::test()` runs through
  `ManageBundles`' actual `CreateAction`/`EditAction` closures (not just
  `BundleService` directly) — these are the ones to look at first if you
  change the resource's form, since Filament's `Repeater` has a real
  testing gotcha: it seeds one UUID-keyed blank item on mount, so filling
  it via `callAction(data: [...])`/`fillForm()` leaves that stray empty
  item behind and fails its own validation. Replace the whole array with
  one `->set('mountedActionsData.0.tiers', [...])` (or
  `mountedTableActionsData.0.tiers` for the edit table action) instead.
- **OrderRendering** — actually renders (not just reads) the three
  Blade/mail surfaces that carry bundle data: the order confirmation
  email's `order-table` partial, the admin `bundle-preview` modal, and the
  cart's `CartItems` Livewire component with a real bundle line in
  session. These exist because this feature's bugs kept showing up only
  on execution, not on review — the `draft`-status product crash and the
  Repeater UUID gotcha above were both missed by reading the code first.

Run with `php artisan test --filter=Bundle`. Needs a real MySQL
connection (`DB_HOST`/`DB_PORT` env overrides work fine if the app's
`.env` points at a Docker-only hostname) and enough CLI `memory_limit` to
run the full suite (`php -d memory_limit=1G artisan test`) — both
unrelated to this feature, just the existing local setup.
