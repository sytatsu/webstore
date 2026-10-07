# Webstore settings admin pages

The admin "Webstore Settings" area used to be one generic Filament
resource (`WebstoreSettingResource`) editing a raw `key`/`value` row, with
a single form carrying five conditionally-visible field types gated by
`visible(fn ($get) => $get('key') === '...')`. In practice this meant:

- `navigation_fdm_printing_handles` had **no field at all** — nothing in
  the form matched that key, so it could only ever be set by editing the
  database directly.
- `collections_page_title` / `collections_page_description` used a
  `TranslatedText` field that was forced `visible(fn ($get) => false)` —
  dead code, permanently unreachable.
- `collections_page_collections` and `home_featured_collections` shared
  one `Repeater` of collection `Select`s, dehydrating to a plain array of
  **collection ids** — but `collections_page_collections` is consumed as
  **slugs** (`CollectionsPage::render()`'s `whereIn('slug', $handles)`).
  Editing it through the admin silently saved the wrong data shape.
- None of these fields reordered anything — `navigation_collection_groups`
  used a `TagsInput` (free-text, no validation against real group
  handles, no drag reordering), and even if it had supported reordering,
  `Navigation.php` resolved the dropdown via `whereIn('handle', ...)`,
  which doesn't honor array order anyway.
- Live `home_featured_collections` data had actually rotted into
  `["11","21",{"collection_id":"11"},{"collection_id":"21"}]` — a mix of
  raw ids and partially-hydrated repeater rows — from exactly this kind
  of hydrate/dehydrate mismatch over past edits.

## What changed

Three keys that each have a real, typed shape and (for two of them) an
order that should actually matter got their own dedicated Filament page,
following the same pattern `HomepageHeroSettingsPage` / `BarBuilderSettingsPage`
/ `BarBuilderDefaultArrangementPage` already use — a `Page` with its own
`form()`/`save()`, not a `Resource`:

- **`NavigationSettingsPage`** (`navigation_collection_groups`,
  `navigation_fdm_printing_handles`) — two `Repeater`s of a single
  `Select` each, `->reorderable()`. One picks which collection groups
  (and in what order) populate the "Collections" dropdown; the other
  picks which individual collections (by slug, in what order) populate
  the "FDM Printing" dropdown, finally giving that setting an editable
  field at all.
- **`HomeFeaturedCollectionsSettingsPage`** (`home_featured_collections`)
  — one reorderable `Repeater` of collection `Select`s, dehydrating to a
  clean array of ints. `mount()` normalizes whatever shape is already on
  disk (`normalizedIds()`) — numeric strings, nested
  `{"collection_id": ...}` rows, duplicates — into that clean array
  *before* it reaches the form, and saving writes the normalized result
  back. This is what actually fixes the live corrupted value, not just a
  nicer form on top of it.
- **`CollectionsPageSettingsPage`** (`collections_page_title`,
  `collections_page_description`, `collections_page_collections`) — a
  per-locale fieldset for title/description (`title.en`/`title.nl`, same
  convention as `Concerns\HasTranslatableName`), and a reorderable
  `Repeater` of collection `Select`s dehydrating to **slugs** this time,
  matching what `CollectionsPage::render()` actually consumes.

All three are registered on the `WebstoreSettings` cluster in
`AppServiceProvider`'s `->pages([...])` (Filament pages here are listed
explicitly, not auto-discovered).

`WebstoreSettingResource` stays, as the fallback for everything else
(and the only place that still lists every raw row, including genuinely
orphaned keys like `hero_collection_buttons`, which nothing in the
codebase reads). Its form no longer has the five conditional fields: a
key handled by one of the pages above now shows a `Placeholder` pointing
at that page instead of an editable field (`DEDICATED_PAGE_KEYS`), so
editing it here can't reintroduce a shape the dedicated page doesn't
expect; every other key gets a generic JSON `Textarea`
(`json_decode`/`json_encode` on dehydrate/hydrate) instead of the old
numeric-only and translated-text fields, so nothing is left with no
field at all the way `navigation_fdm_printing_handles` used to be.

## Making "reorder" actually do something

A `Repeater` that reorders is pointless if the storefront code ignores
the order. Two read paths filtered by `whereIn(...)` without ever
honoring the array's order, so reordering in the old `TagsInput` (for
the one key that had a field) had no visible effect even before it was
replaced:

- `Navigation.php` now groups the query result by handle/slug first,
  then walks the *stored settings array* itself to rebuild the final
  list in that order, for both the collection-groups dropdown and the
  FDM-printing dropdown.
- `CollectionsPage.php` does the same for `collections_page_collections`.

Verified live: setting `navigation_collection_groups` to
`['fdm-printing', 'printed']` (reversed from the default) moved
"Polymaker" to the front of the "Collections" dropdown, ahead of the
`printed` group's own collections — confirming the order set on
`NavigationSettingsPage` is what the storefront actually renders now,
not just what the admin form displays.

## Follow-up: the Clickerz Bar CTA became a configurable element, not an automatic one

The Clickerz Bar CTA (homepage promo banner, nav link) used to render
automatically in one fixed spot whenever `BarBuilderSettingsPage::isEnabled()`
was true — invisible from both `HomeFeaturedCollectionsSettingsPage` (then
still named "Homepage Collections") and `NavigationSettingsPage`, so there
was no way to see or change where it sat relative to the featured
collections or the other nav items. Both pages now treat it as one more
row an admin places explicitly:

- **`HomeFeaturedCollectionsSettingsPage`** (renamed **"Homepage Elements"**
  to match) — its `elements` Repeater rows are now typed
  (`{"type": "collection", "collection_id": ...}` or `{"type": "clickerz"}`),
  picked via a `type` Select per row (`->live()`, toggles which sibling
  field shows). `Welcome.php::getHomepageElementsAttribute()` resolves
  collection rows to their `ProductCollectionDTO` (dropping any whose
  collection no longer exists) and drops a `clickerz` row entirely when
  the Bar Builder is disabled or the hero is already the Clickerz hero —
  both gates live together in that one method now, not split between
  `welcome.blade.php`'s old fixed `@if` and the CTA component's own
  comment. `welcome.blade.php` renders one Livewire `collection-cards`
  instance per collection row (it already accepted a single
  `ProductCollectionDTO`) interleaved with `<x-sytatsu.homepage.clickerz-cta>`,
  wrapped in one shared `gap-8` flex container for spacing, instead of a
  single instance handling every collection at once.
- **`NavigationSettingsPage`** gained a fourth Repeater, `top_level_order`
  (setting key `navigation_top_level_order`), reordering/toggling the nav
  bar's four top-level entries themselves — `collections`, `clickerz`,
  `fdm_printing`, `services` — independently of the `groups` and
  `fdm_printing_collections` Repeaters below it, which still only control
  *what's inside* the Collections/FDM Printing dropdowns, not whether or
  where those dropdowns themselves appear. `Navigation.php` reads the
  stored order, filters it to known types
  (`NavigationSettingsPage::normalizedTopLevelOrder()`), and further drops
  `clickerz` (Bar Builder disabled) or `fdm_printing` (no FDM collections
  configured) per-render rather than baking those gates into the stored
  value — so enabling the Bar Builder later doesn't require re-saving this
  page just to make an already-stored `clickerz` entry start rendering.
  `navigation.blade.php`'s old fixed sequence of markup blocks became one
  `@foreach` + `@switch`, each `@case` holding exactly the markup that
  item already had.

Both changes needed `home_featured_collections`'s two seeder commands
(`SeedPokeballCollectionCommand`, `SeedMiniFriendsCollectionCommand`)
updated too — they used to check `in_array($collectionId, $featured)`
against a flat array of ints, which would never match the new typed-row
shape and re-add the same collection on every re-run.

## Scope note: navigation stays a group/slug picker, not a free nav editor

The user was asked directly whether "rearranging... navigation items"
should mean reordering/toggling the *existing* collection groups and FDM
collections (today's data model — labels always come from the group's
own translated name), or a fully flexible nav editor (custom labels,
arbitrary link targets, items that aren't a collection group at all —
which would also require reworking how the dropdowns themselves render,
not just the admin form). They chose the former. `NavigationSettingsPage`
is built on that basis — it is deliberately not a general-purpose nav
builder.
