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
- `AddToCart`'s "Add to bundle" button also disables itself once a
  product's own available stock is reached, so the common case never even
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
that, seeds the **session** selection from `BundleService::selectionForCartLine()`
(overwriting whatever was already queued there), and `addToCart()` becomes
an edit (`updateCartLine()`) for that line id.

## The bundle builder is the only way to acquire these products

A bundle-eligible product cannot be added to the cart on its own, from
anywhere — not the collection grid, not the product's own detail page.
`App\Http\Livewire\Sytatsu\Components\AddToCart` (the one component both
`product-tile.blade.php` and `product.blade.php` already used for the
normal "Add to shopping cart" button) resolves
`BundleService::findActiveBundleForProduct()` on `mount()`; when that
returns a `Bundle`, its Blade renders an "Add to bundle" / stepper /
"Remove" control instead of the normal price+quantity+add-to-cart UI, and
`addToCart()` itself refuses to run even if called directly (defence in
depth beyond just hiding the button — never trust the client alone to
enforce "this product can't be bought on its own").

### Why the in-progress selection lives in the session, not a component

`AddToCart` on a product's own detail page and the `BundleBuilder` tray on
the collection page are **separate Livewire components mounted on
separate page loads** — there's no shared PHP object between them. So the
"what's queued right now for this bundle" state can't live in either
component's own properties; it's kept in
`BundleService::getSessionSelection()` / `updateSessionSelectionItem()`
instead (keyed `bundle_selection_{id}`), which every bundle-aware
component reads from and writes through on every page load. Livewire's
cross-component events
(`bundle-item-picked` dispatched by `AddToCart::addToBundle()`/
`removeFromBundle()`, `bundle-selection-updated` broadcast back by
`BundleBuilder`) only keep controls *on the same page* instantly in sync
with each other — the session is what makes the picture consistent
*across* pages.

## Storefront pieces (and why the collection page itself barely changed)

- `CollectionPage::mount()` resolves the active bundle via
  `BundleService::findActiveForCollection()`, which checks the collection
  **and its ancestors** — so a sub-collection page (Safari, Ocean, …) picks
  up the bundle configured on the parent (Mini-friends), and the picker's
  eligible product set is always the *whole* bundle collection's tree
  (`eligibleProducts()`), not just the sub-collection being browsed. That's
  what lets a bundle mix items across sibling sub-collections.
- `resources/views/sytatsu/webstore/collection.blade.php` gained exactly
  **one additive line**: `<livewire:...bundle-builder>`, gated behind
  `@if($bundle ?? null)`. The filters/grid/pagination markup, and every
  product tile (`<livewire:...product-tile>`), are completely untouched —
  when there's no bundle for a collection, this file renders byte-for-byte
  what it did before. There is no toggle to reveal the tray or the pick
  controls — both are simply always there when a bundle applies, because
  "the bundle builder is the only way to pick products" means there's
  nothing to toggle *into*.
- `App\Http\Livewire\Sytatsu\Pages\Webstore\ProductPage` resolves the same
  active bundle for whatever product it's showing and renders the same
  `BundleBuilder` tray there too, so picking from a product's own page
  still shows the running total.
- The tray (`bundle-builder.blade.php`) renders as the **first** thing in
  the page content — above the grid on the collection page, above the
  gallery/options card on a product's own page — and is `sticky` with a
  `top` offset kept in sync with `#site-header`'s *current* height via a
  small `ResizeObserver` (`navigation.blade.php`'s header height changes
  as it shrinks on scroll, so a hardcoded pixel offset would drift). This
  replaced an earlier `fixed bottom-0 inset-x-0` version: that guaranteed
  "always on screen" regardless of scroll position too, but the user
  wanted it docked under the header at the top instead, not floating over
  the last grid row — which is also why the `pb-28` bottom-padding
  compensation on the page wrapper is gone; a `sticky` element occupies
  its own space in the normal flow, so nothing needs to make room for it.
- Each eligible tile's own price (`ProductTile::getPriceRangeString()`) is
  hidden once the product has an active bundle — same
  `BundleService::findActiveBundleForProduct()` check `AddToCart` already
  uses, resolved independently by `ProductTile::mount()`. The product's own
  price is meaningless once the bundle is the only way to buy it; what the
  customer actually pays is the tier price shown in the tray.
- The tray has a "How does this work?" button (plain Alpine `x-data`
  modal, matching `components/cookie-policy-popup.blade.php`'s pattern —
  no Livewire round-trip needed) that lists `$bundle->tiers()` with their
  prices. This can never drift from what checkout actually charges because
  it reads the same `Price` rows `BundleService::syncPurchasable()` keeps
  in sync, not a separate copy.

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

- **No live auto-hide on "Bundle added!".** The success state in
  `bundle-builder.blade.php` stays until the next pick; the old branch had
  a 3-second `setTimeout` auto-hide (Alpine `x-data` + `x-on:bundle-added-success.window`)
  that's a reasonable thing to port over if it's missed.
- **`edit_bundle_line` is a plain query string, not a Livewire `queryString`
  property** on `CollectionPage`. It's only read at the initial full-page
  load (`mount()`/the first `render()`), which is correct for the "click
  Edit in the cart" flow, but don't expect it to survive being manually
  edited mid-session via a Livewire AJAX update.
- **The in-progress selection lives in the PHP session**, same as a
  guest's cart in general — it doesn't follow a customer across browsers
  or devices, and clearing cookies loses it. That's an accepted trade-off
  for "works across separate page loads without a shared Livewire
  component," not something to fix.

## Tests

35 tests, `tests/Feature/Bundle{Pricing,Eligibility,Cart,Admin,AdminHttp,BuilderComponent,OrderRendering}Test.php`:

- **Pricing** — tier resolution at/between thresholds, re-saving tiers
  replaces rather than duplicates `Price` rows.
- **Eligibility** — descendant sub-collection products are included,
  out-of-stock/unpublished products are excluded, a sub-collection page
  surfaces its parent's bundle, disabled bundles are never found.
- **Cart** — adding a bundle produces exactly one cart line with the
  matched tier price and a full item list in `meta`; stock is clamped and
  reported; editing a line replaces it rather than duplicating it.
- **BuilderComponent** — the real `AddToCart`/`BundleBuilder` Livewire
  components, not just `BundleService` directly:
  - `AddToCart` swaps to "add to bundle" / stepper / "remove" for an
    eligible product, caps at stock, and — proven with an explicit test —
    its `addToCart()` *server action* refuses to run even when called
    directly, not just hidden in the Blade.
  - the cross-page session handshake: picking via `AddToCart` on one
    simulated "page" (one `Livewire::test()` instance) and reading it
    back via `BundleService::getSessionSelection()` or a *different*
    component instance proves state really does survive across separate
    mounts, which is the entire point of storing it in the session rather
    than on either component.
  - the collection page and a product's own detail page both render the
    always-on tray and an "Add to bundle" button, never "Add to shopping
    cart" or a "Start building" toggle.
  - the full edit-link composition, end to end, through a real HTTP
    request rather than `Livewire::test()` alone: a cart line's "Edit" link
    (`?edit_bundle_line=`) makes `BundleBuilder::mount()` write the line's
    selection into the session, and on that *same* request every eligible
    product tile's own `AddToCart::mount()` must read it back and start
    its stepper at the right quantity — relying on `BundleBuilder`
    rendering (and thus mounting) before the product grid in
    `collection.blade.php`. This is the one seam nothing else exercises
    (each half had its own test, never both together), and it genuinely
    failed once while writing this test: a `Livewire::test()` call alone
    doesn't carry a real query string into `request()->integer(...)`
    inside `render()`, so the first version of this test silently asserted
    against an unseeded page. Fixed by driving it through `$this->get()`
    against the real route instead.
  - the one real bug this caught: a stock-clamped selection still counts
    as `added` (the cart line *was* created, just smaller than asked), so
    `BundleBuilder` must key off the `added` flag
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
