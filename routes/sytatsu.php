<?php

use App\Http\Livewire\Sytatsu\Pages as LivewireSytatsu;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes | https://sytatsu.nl/
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Dutch (nl) is the default locale and lives at the root with no prefix; English is
// served under /en/, with every route name suffixed ".en". Laravel's router can't cleanly
// match a single route with an optional {locale?} prefix followed by required segments
// (confirmed: it only matches the "no locale" case for a bare "/" route, nothing deeper),
// so each locale gets its own real, unambiguous set of routes instead. Every existing
// route($name, ...) call in the app keeps working unchanged because App\Support\
// LocaleAwareUrlGenerator (bound in AppServiceProvider) transparently resolves to the
// ".en" variant when the current locale is English — see that class for details.
if (! function_exists('registerWebstoreRoutes')) {
function registerWebstoreRoutes(?string $nameSuffix = null): void
{
    Route::get('/about', LivewireSytatsu\About::class)->name("sytatsu.about{$nameSuffix}");
    Route::get('/custom-print', LivewireSytatsu\CustomPrint::class)->name("sytatsu.custom-print{$nameSuffix}");
    Route::get('/maintenance-repair', LivewireSytatsu\MaintenanceRepair::class)->name("sytatsu.maintenance-repair{$nameSuffix}");
    Route::get('/contact', LivewireSytatsu\Contact::class)->name("sytatsu.contact{$nameSuffix}");

    Route::get('/', LivewireSytatsu\Webstore\Welcome::class)->name("sytatsu.webstore.welcome{$nameSuffix}");
    Route::get('/products/{product}', LivewireSytatsu\Webstore\ProductPage::class)->name("sytatsu.webstore.product{$nameSuffix}");
    Route::get('/clickerz/builder', LivewireSytatsu\Webstore\ClickerzBarBuilderPage::class)->name("sytatsu.webstore.clickerz-bar-builder{$nameSuffix}");
    Route::get('/collections', LivewireSytatsu\Webstore\CollectionsPage::class)->name("sytatsu.webstore.collections{$nameSuffix}");
    Route::get('/collections/{collection}', LivewireSytatsu\Webstore\CollectionPage::class)->name("sytatsu.webstore.collection{$nameSuffix}");

    Route::get('/checkout/success', LivewireSytatsu\Webstore\CheckoutSuccessPage::class)->name("sytatsu.webstore.checkout.success{$nameSuffix}");

    Route::middleware('has.cart')->group(function () use ($nameSuffix) {
        Route::get('/cart', LivewireSytatsu\Webstore\CartPage::class)->name("sytatsu.webstore.cart{$nameSuffix}");
        Route::get('/checkout', LivewireSytatsu\Webstore\CheckoutPage::class)->name("sytatsu.webstore.checkout{$nameSuffix}");
    });
}
}

registerWebstoreRoutes();

Route::prefix('en')->group(function () {
    registerWebstoreRoutes('.en');
});


// The 'product'/'collection' route-model bindings (slug lookup via the Lunar Url table,
// with a 301 redirect off stale slugs) live in App\Providers\AppServiceProvider instead of
// here. Route::model() calls in this file only run when routes are NOT cached — Laravel
// skips re-executing routes/*.php entirely once `route:cache` has run, loading the compiled
// route table instead — while Lunar's ModelManifest registers its own plain-Eloquent
// 'product'/'collection' binders unconditionally on every request. With the override sitting
// here, a cached-routes production deploy would silently fall back to Lunar's default binder
// (lookup by id, not slug) and 404 every product/collection page. See AppServiceProvider.

// Ensures unmatched URLs still run through the `web` middleware group (session + Locale)
// before falling through to the 404 handler, so the visitor's selected locale is honoured.
Route::fallback(function () {
    abort(404);
});
