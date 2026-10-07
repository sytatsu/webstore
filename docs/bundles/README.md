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
- The tray is wider than the page's own content (`max-w-[95rem]`) while
  sitting in its normal flow position, easing down to the page's own
  width (`max-w-[85rem]`) once scrolling has actually pinned it under the
  header — tracked via the tray's own `getBoundingClientRect()` vs the
  dynamic header-height offset, not a separate sentinel element (wrapping
  it in one more plain ancestor div to hold that state broke native
  `position: sticky` outright). It has **no horizontal padding of its
  own** — the page layout (`sytatsu-layout.blade.php`) already pads its
  whole `$slot`, same as the filter/grid row below it; adding more on top
  of that made the tray narrower than that row instead of matching it.
  Its fill is a solid, fully opaque colour (`bg-orange-50` /
  `dark:bg-slate-800`), not a translucent gradient — a gradient through a
  primary-tinted alpha stop used to sit here, which let the page's own
  background bleed through at that edge instead of reading as one
  consistently-coloured card. It's since moved from a light orange tint
  to plain white/`dark:slate-800` — the same fill every other card on
  the page (filters, sort, the grid itself) uses — so the border is what
  marks it as different, not a competing background colour.
- The tray shows the **total** cost of what's picked so far (tier price
  × quantity), not just the per-item price — rendered next to the
  "Add bundle to cart" button, computed inline from `$this->currentTier`
  and `$this->totalQuantity` rather than a new computed property, since
  both were already public.
- The collection's own name is the page's `<h1>`, above everything
  (including the tray) — it used to sit squeezed directly above the
  product grid instead, which started competing with the grid for
  attention once the tray's own name label landed right above *that*.
  It's in its own card too (matching every other section), with a
  visible breadcrumb trail above it built from `$breadcrumbItems` —
  previously computed only for the page's JSON-LD schema, never
  actually rendered for a visitor to see.
- `components/livewire/search-box.blade.php` (the header's live search
  dropdown) has its own hand-rolled product row — it doesn't go through
  `ProductTile`/`AddToCart` — so it needed its own
  `BundleService::findActiveBundleForProduct()` check
  (`SearchBox::activeBundleFor()`) to swap in the bundle's own
  lowest-tier price instead of the product's normal one. Easy to miss
  since it's a separate code path from every other price-hiding fix in
  this feature; if another storefront surface ever renders its own
  product price outside those two components, check it here too.
- The page's title card, the tray and the filter/grid row all stack with
  the page's usual `gap-8` — shrinking that to `gap-4` (and the title
  card's own `py-6` to `py-4`) was tried first to close up what looked
  like extra space above the tray, but that tightened every section's
  spacing page-wide, not just the one that actually had a problem, and
  was reverted. The real surplus was the tray's own flat `pt-4`
  stacking on top of that `gap-8` (see the next point) — fixing that
  instead left every section-to-section gap on the page, including the
  one above the tray, matching.
- The tray's top padding (`pt-4`) is conditional on its own `stuck`
  state (`:class="stuck ? 'pt-4' : ''"`), not a flat class. It exists to
  keep the tray from touching the site header once scrolling has pinned
  the two together — it has no reason to also apply while the tray is
  resting in its normal position right below the title card, where the
  page's own `gap-8` already provides the right amount of space. Left
  flat, it stacked on top of that `gap-8`, making the gap above the
  tray bigger than every other section-to-section gap on the page.
- The tray's picked-items row needs `pt-2`, not `py-1`: `overflow-x-auto`
  (for horizontal scrolling when there are more picks than fit) forces
  the paired `overflow-y` to compute as `auto` too — you can't mix
  `auto` on one axis with `visible` on the other — so that row clips
  anything poking outside it, and the quantity badge deliberately sits
  `-top-1.5` above its own thumbnail. Same root cause as the cart's own
  badge-clipping fix above, different element.
- A sticky filter/sort sidebar (desktop only, docking below the tray
  when one applies, below just the header otherwise) was tried and
  reverted at the user's request — not a bug, just not wanted here.
- Every picked-item thumbnail (tray, cart, checkout success page) has a
  hover tooltip showing the product's name, on top of the `title`
  attribute each already had. It's a small `x-data` block
  (`tipShown`/`tipX`/`tipY`, set from the thumbnail's own
  `getBoundingClientRect()` on `@mouseenter`) rendering a
  `position: fixed` bubble, not a normal `absolute` one — the tray's
  row is `overflow-x-auto` (see the `pt-2` note above), which clips
  anything `absolute` that pokes outside it, and `fixed` escapes that
  clipping entirely since its containing block is the viewport, not
  the row. Used uniformly in all three places even though only the
  tray's row actually has the clipping problem, so the same small block
  doesn't need two different implementations. Each copy also carries a
  static `style="display: none;"` alongside `x-show`/`x-cloak` — without
  it the tooltip is briefly (or, if Alpine fails to attach at all, as
  happened testing this in a bare non-Livewire page, permanently)
  visible by default, same convention as the tray's own "How does this
  work?" modal.
- Each eligible tile's own price (`ProductTile::getPriceRangeString()`) is
  hidden once the product has an active bundle — same
  `BundleService::findActiveBundleForProduct()` check `AddToCart` already
  uses, resolved independently by `ProductTile::mount()`. The product's own
  price is meaningless once the bundle is the only way to buy it; what the
  customer actually pays is the tier price shown in the tray.
- A bundle-eligible tile is one big "add to bundle" click target, not just
  the explicit button at the bottom — product-tile.blade.php's root div
  gets an Alpine `@click` that forwards to whichever control
  add-to-cart.blade.php is currently showing (the initial "Add to bundle"
  button, or the stepper's "+" once something's picked; both carry
  `x-ref="bundleAddTrigger"`), guarded so clicking an actual `a`/`button`/
  `input` (the title, the stepper itself) does its own thing instead. This
  has to be `$el.querySelector('[x-ref=bundleAddTrigger]')`, not Alpine's
  `$refs` magic: add-to-cart is a *separate* Livewire component, and every
  Livewire v3 component root is itself an implicit Alpine scope, so a ref
  inside it is invisible to `$refs` from an ancestor outside that
  component (confirmed empirically — `$refs` came back `undefined` for an
  element that was a plain DOM descendant). The carousel's own image also
  used to link to the product page in this state; that's now
  conditional too (`Carousel::$linkToProduct`, off when
  `ProductTile::$activeBundle` is set) so clicking the image adds to the
  bundle instead of navigating away — the title link is untouched either
  way, and is the only thing that still goes to the detail page.
- The tray has a "How does this work?" button (plain Alpine `x-data`
  modal, matching `components/cookie-policy-popup.blade.php`'s pattern —
  no Livewire round-trip needed) that lists `$bundle->tiers()` with their
  prices. This can never drift from what checkout actually charges because
  it reads the same `Price` rows `BundleService::syncPurchasable()` keeps
  in sync, not a separate copy.
- The tray plays a brief pulse (scale + expanding ring, `--animate-bundle-pulse`
  in `resources/scss/sytatsu.scss`) whenever a product is actually added.
  `AddToCart::addToBundle()` dispatches a dedicated, payload-less
  `bundle-item-added` browser event for this — deliberately separate from
  `bundle-item-picked`, which also fires on every decrement/removal (same
  method handles all three), so a removal doesn't also pulse. The tray's
  own Alpine listener resets `pulsing` to `false` and back to `true` on a
  `$nextTick` rather than just setting it `true`, so a second add while
  the first pulse is still playing actually restarts the animation
  instead of silently no-op'ing (Alpine's `:class` binding doesn't
  re-trigger a CSS animation from a value that doesn't change).
- A bundle collection listed on the homepage (`collection-cards.blade.php`,
  shared with the parent-collection sub-collection listing) gets a thin,
  one-row CTA instead of its usual product grid — `BundleService::findActiveForCollection()`
  per listed collection decides which. There's no single "add to cart" for
  any one of a bundle's products from that far away (no tray mounted on
  this page), so a grid of tiles that can't really be bought would be
  misleading; a link back to the bundle's own collection page instead.
  The collection's own image (the same `collection_image` attribute /
  thumbnail fallback used by `collections.blade.php`) sits flush to the
  card's own top/left/bottom edges with no padding/margin of its own —
  `absolute inset-y-0 left-0 w-28` against the card itself (the card
  gained `relative`; the row keeps its normal padding and is no longer
  involved in sizing the image at all), not a flex sibling using
  `self-stretch`. That distinction matters: a flex item's `self-stretch`
  cross-size is resolved from the *hypothetical* (un-stretched) size of
  every item in the line first, and an `<img>` with `h-full` but no
  definite parent height falls back to its own native aspect ratio at
  the given width for that pass — so the bundle CTA and the Clickerz CTA
  (whose source photos have different native aspect ratios) rendered at
  two different heights under that approach, confirmed via
  `getBoundingClientRect()` on both. Taking the image out of flex flow
  entirely removes that dependency: its box is always exactly the
  card's own height × `w-28`, regardless of the photo's own dimensions
  or the label's content length, with `object-cover` filling it and an
  angled clip-path on the inner (right) edge only (ticket-stub style).
  The icon/label block gets `sm:ml-28` to clear the image instead of
  sharing a flex item with it. Hidden below `sm`, where the row already
  stacks label-above-button, so a fourth element would only crowd it. A
  Blade comment on this partial must never spell out a literal
  `@directive(...)`-looking token (even just to describe
  one in prose) — Blade's compiler matches those inside `{{-- --}}`
  comments too, which silently corrupts everything compiled after it;
  confirmed the hard way while building this.
- The shared page header (`x-sytatsu.page-header`, breadcrumb + title in
  its own card) started on the product collection page and now also
  covers the collections listing, product detail, custom print,
  maintenance and contact pages — see the component itself for the
  exact markup. Keep any future "page header" request going to that one
  component instead of growing another bespoke title block.
- The Clickerz Bar Builder gets the same thin one-row CTA treatment on
  the homepage (`x-sytatsu.homepage.clickerz-cta`), even though it isn't
  a collection and so never passes through `collection-cards.blade.php`
  — it's its own component with the identical full-bleed-image markup
  (now a real photo, `resources/images/banners/p1020972-clickerz-keycaps.jpg`,
  not the SVG logo it started with). Unlike the bundle CTA it's not tied
  to any one collection, so where it sits is admin-configured directly:
  it's one more row an admin can drag into
  `HomeFeaturedCollectionsSettingsPage`'s ("Homepage Elements") ordered
  list, dropped at render time (`Welcome.php::getHomepageElementsAttribute()`)
  whenever `BarBuilderSettingsPage::isEnabled()` is false or the homepage
  hero is already the Clickerz hero — stacking two Clickerz promos
  back-to-back would be redundant in a way the bundle hero + bundle CTA
  pairing isn't, since those sit much further apart on the page. See
  `docs/webstore-settings/README.md` for the rest of that page's typed-row
  shape.

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
- **OrderRendering** — actually renders (not just reads) the four
  Blade/mail surfaces that carry bundle data: the order confirmation
  email's `order-table` partial, the `order-success.blade.php` checkout
  success page, the admin `bundle-preview` modal, and the cart's
  `CartItems` Livewire component with a real bundle line in session.
  These exist because this feature's bugs kept showing up only on
  execution, not on review — the `draft`-status product crash and the
  Repeater UUID gotcha above were both missed by reading the code first.
  `order-success.blade.php` in particular has its own hand-rolled line
  loop (it predates this feature and isn't built from `order-table`'s
  partial), so it was missed entirely on the first pass — same class of
  bug as `search-box.blade.php`'s own separate price-display code path
  above: any surface that renders an order/cart line without going
  through `order-table.blade.php` or `items.blade.php` needs its own
  `meta['bundle']` branch, and is easy to miss since nothing fails to
  compile when it's skipped.

Run with `php artisan test --filter=Bundle`. Needs a real MySQL
connection (`DB_HOST`/`DB_PORT` env overrides work fine if the app's
`.env` points at a Docker-only hostname) and enough CLI `memory_limit` to
run the full suite (`php -d memory_limit=1G artisan test`) — both
unrelated to this feature, just the existing local setup.
