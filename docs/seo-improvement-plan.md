# SEO Improvement Plan

Audit date: 2026-08-04. Stack: Laravel 12, Livewire 3 (full-page components, server-rendered — no `wire:navigate` SPA mode found), Lunar e-commerce.

This document is a prioritized, actionable roadmap. Each item names the concrete file(s) it touches. Items are grouped into three tiers by effort/impact so work can start anywhere without needing to do everything at once.

## What's already working (keep it that way)

- Product images consistently have `alt` text and `loading="lazy"` (`resources/views/sytatsu/components/livewire/product/carousel.blade.php`).
- Pages are rendered as standard full server-rendered HTML per request — no `wire:navigate` SPA mode in use anywhere. Crawlers see fully-rendered content on first load. **Don't switch this to SPA-style navigation without adding proper prerendering** — it would be a regression.
- A `google-site-verification` meta tag mechanism already exists (`config('seo.google-site-verification')`), confirming there's existing intent to use Search Console — it's just currently broken (see Tier 1).

---

## Tier 1 — Quick wins (hours, no architecture change)

### 1.1 Fix the robots.txt confusion
Two files exist with conflicting content:
- `public/robots.txt` (the one Laravel actually serves): `Disallow:` — blocks nothing.
- Project-root `/robots.txt` (dead — not web-accessible): has a `Crawl-delay: 10`, disallows `/login` and `/images/favicons`, and references `Sitemap: sitemap.xml`, which doesn't exist.

**Action**: Decide on one real policy and put it in `public/robots.txt`. Once Tier 2's sitemap exists, add `Sitemap: https://sytatsu.nl/sitemap.xml` (absolute URL) to it. Delete or repurpose the stale root-level file so it doesn't mislead future editors into thinking it's live.

### 1.2 Fix the Google Search Console verification env typo
`config/seo.php:4` reads `env('GOOGLE_SITE_VERIFICATION_', '')` (trailing underscore), but `.env` / `.env.example` define `GOOGLE_SITE_VERIFICATION` (no underscore). The verification meta tag has been silently empty this whole time.

**Action**: Remove the trailing underscore in `config/seo.php`. Then verify site ownership in Google Search Console.

### 1.3 Replace the static, irrelevant `<meta name="keywords">`
`resources/views/layouts/sytatsu-layout.blade.php` (and `checkout-layout.blade.php`) ship a keyword list that reads like it was copied from a personal developer portfolio: `HTML,CSS,JavaScript,PHP,React,Laravel,Symphony,...,StPronk,Steve,Pronk,...,Zoetermeer,...`. This is unrelated to a 3D-print storefront and adds no SEO value (major engines ignore or lightly weight this tag today).

**Action**: Either remove the tag entirely, or replace it with a short, genuinely relevant list (e.g. `3D printing, custom prints, Clickerz Bar, 3D models, Zoetermeer`). Low priority for ranking impact, but it's a visible embarrassment if anyone views source.

### 1.4 Give every page a real, unique `<title>`
`BasePage::setTitle()` / `LayoutService::formatTitle()` (`app/Http/Livewire/BasePage.php`, `app/Services/LayoutService.php:12-23`) already produce `"{Page Title} - {App Name}"` when a title is set. Today only `ProductPage`, `CollectionPage`, `CollectionsPage`, and `ClickerzBarBuilderPage` call `setTitle()`. The rest (`About`, `Contact`, `CustomPrint`, `MaintenanceRepair`, `Welcome`, `Cart`, `Checkout`, `CheckoutSuccessPage`) fall back to just the app name — a missed, free keyword opportunity and a duplicate-`<title>` problem across pages.

**Action**: Add a `setTitle()` call in each of those components' `mount()`, e.g. `App\Http\Livewire\Sytatsu\Pages\About`, `Contact`, `CustomPrint`, `MaintenanceRepair`.

### 1.5 Add a canonical tag to the layouts
No `<link rel="canonical">` exists anywhere. Without it, any URL variant (trailing slash, query params, old slugs) can be seen as separate content by search engines.

**Action**: Add `<link rel="canonical" href="{{ url()->current() }}">` to `resources/views/layouts/sytatsu-layout.blade.php` and `checkout-layout.blade.php` as a baseline. Tier 2 refines this for collection filter query strings.

---

## Tier 2 — Structural improvements (days)

### 2.1 Wire real per-page meta descriptions end-to-end
`CollectionsPage` already builds a `description` value (`app/Http/Livewire/Sytatsu/Pages/Webstore/CollectionsPage.php`) but only passes it as a **view** attribute (page body content) — it never reaches the `<meta name="description">` tag, which stays hard-coded and identical on every page.

**Action**: Mirror the existing `title` plumbing:
- Add `description`/`getDescription()`/`setDescription()` to `BasePage` next to the existing `title` methods.
- Have `LayoutService::render()` pass `description` into layout attributes the same way it does `title`.
- Update `sytatsu-layout.blade.php` / `checkout-layout.blade.php` to output `{{ $description ?? config('app.description_fallback') }}` for the meta description.
- Populate it per page type: product pages → short/truncated product description; collection pages → collection description; static pages (About/Contact/etc.) → one curated sentence each.

### 2.2 Add Open Graph + Twitter Card tags
None exist anywhere in the codebase. These directly affect click-through from social shares and search result rich previews.

**Action**: Once 2.1 lands, add `og:title`, `og:description`, `og:image`, `og:url`, `og:type`, `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image` to the layouts, reusing the same title/description values. For image, fall back to the brand logo (`resources/images/brands/no_background_text_only.webp`, already used in `navigation.blade.php` and the error pages) when a page has no specific image (e.g. product's main image on product pages).

### 2.3 Generate an XML sitemap
No sitemap package, route, or generated file exists anywhere in the project.

**Action**: Install `spatie/laravel-sitemap`. Add an Artisan command (following the project's existing pattern of moving logic into dedicated commands — see the `43ade53` "Main functionality from the seeders moved to separate commands & migrations" commit) that builds the sitemap from:
- Static routes (`/`, `/about`, `/contact`, `/custom-print`, `/maintenance-repair`, `/collections`)
- All products and collections via Lunar's `Url` model, using `default = true` entries only, with `<lastmod>` from `updated_at`
Schedule the command (e.g. daily) in `app/Console/Kernel.php`, output to `public/sitemap.xml`, and reference it from `public/robots.txt` (Tier 1.1).

### 2.4 Redirect historical product/collection slugs (301)
`routes/sytatsu.php:42-68` resolves the `{product}`/`{collection}` route-model bindings via Lunar's `Url` model, ordering by `default desc, id desc` and taking the first match — meaning an **old slug still resolves and renders directly**, with no redirect to the current canonical URL. This creates duplicate-content URLs every time a product/collection is renamed.

**Action**: In the `Route::model()` closures in `routes/sytatsu.php`, when the matched `Url` record is not the default one, issue a `redirect(..., 301)` to the default URL's slug instead of returning the model directly.

### 2.5 Canonicalize collection filter URLs
`CollectionPage` has five query-string filter params (`subCollections`, `minPrice`, `maxPrice`, `inStock`, `sort` — `app/Http/Livewire/Sytatsu/Pages/Webstore/CollectionPage.php`), each producing a distinct, crawlable URL for what is substantively the same collection page (classic faceted-navigation duplicate content).

**Action**: On `CollectionPage`, set the canonical tag (from 1.5) to the base collection URL without query params, regardless of active filters.

---

## Tier 3 — Bigger investments (needs product/business buy-in)

### 3.1 JSON-LD structured data
No structured data exists anywhere (`application/ld+json` returns zero repo-wide matches).

**Action**:
- Sitewide: `Organization` schema in the layout (name, logo, social profile URLs — the footer already has these social links in `resources/views/sytatsu/components/footer.blade.php`).
- Product pages: `Product` schema (name, image, description, sku, price, currency, availability) from `app/Http/Livewire/Sytatsu/Pages/Webstore/ProductPage.php` data.
- Product/collection pages: `BreadcrumbList` schema.
This is what makes Google eligible to show price/availability/rating rich snippets in search results — meaningful CTR impact for an e-commerce site.

### 3.2 URL-based locale strategy (open decision)
Locale is currently **session-only** (`App\Http\Livewire\Sytatsu\Components\LocaleSwitcher::switchLocale()` calls `session(['locale' => $locale])`, read back by `App\Http\Middleware\Locale`; `config/locale-switcher.php` defines `nl`/`en`). There's no URL segment or subdomain per locale, so Google cannot index the Dutch and English versions of a page as separate, distinct URLs, and there's nothing to hreflang-link even if it could.

**This needs a decision before implementation**, since it's a routing-layer rewrite, not a small patch. Recommended direction: path-prefixed routes (`/nl/...`, `/en/...`, e.g. via Laravel's route groups + a locale-prefix middleware), keeping the session-based switcher as a convenience redirect. Add `<link rel="alternate" hreflang="nl">` / `hreflang="en"` / `hreflang="x-default"` tags once URLs are distinct per locale.

### 3.3 Enforce HTTPS in production
`.env` / `.env.example` define `APP_URL=http://sytatsu.local`; no `URL::forceScheme('https')` or `ForceHttps` middleware exists anywhere in `app/`. HTTPS is a confirmed (if minor) Google ranking signal, and it's table-stakes for any e-commerce checkout flow regardless of SEO.

**Action**: In `AppServiceProvider::boot()`, call `URL::forceScheme('https')` when `app()->environment('production')`, set the production `APP_URL` to `https://...`, and add HSTS headers at the webserver/proxy layer.

### 3.4 Core Web Vitals / performance
`vite.config.js` has no image-optimization or CDN-related plugins configured. Page speed (especially LCP) is a ranking factor.

**Action**: Evaluate responsive images (`srcset`/`sizes`) for product images, an image-compression step in the build pipeline, and whether a CDN is worth introducing for `public/build` assets and product imagery.

---

## Open questions for the user

1. **Sitemap package/approach** (2.3) — confirm `spatie/laravel-sitemap` is acceptable, or if there's a preferred alternative/hosting approach for `sitemap.xml`.
2. **URL-based locale routing** (3.2) — this is the single biggest-scope item here (route rewrite across the whole `sytatsu.php` route file). Worth scoping as its own project once Tier 1/2 are done, and needs a decision on prefix style (`/nl/`, `/en/`) vs. subdomain before any code is written.
