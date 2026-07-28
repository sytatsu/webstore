<?php

use App\Http\Livewire\Sytatsu\Pages as LivewireSytatsu;
use Illuminate\Support\Facades\Route;
use Lunar\Models\Url;
use Lunar\Models\Collection;
use Lunar\Models\Product;

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

//Route::get('/', LivewireSytatsu\Welcome::class)->name('sytatsu.welcome');
//Route::middleware('enable.cart')->group(function () {
Route::get('/about', LivewireSytatsu\About::class)->name('sytatsu.about');
Route::get('/custom-print', LivewireSytatsu\CustomPrint::class)->name('sytatsu.custom-print');
Route::get('/maintenance-repair', LivewireSytatsu\MaintenanceRepair::class)->name('sytatsu.maintenance-repair');
Route::get('/contact', LivewireSytatsu\Contact::class)->name('sytatsu.contact');

Route::get('/', LivewireSytatsu\Webstore\Welcome::class)->name('sytatsu.webstore.welcome');
Route::get('/products/{product}', LivewireSytatsu\Webstore\ProductPage::class)->name('sytatsu.webstore.product');
Route::get('/clickerz/builder', LivewireSytatsu\Webstore\ClickerzBarBuilderPage::class)->name('sytatsu.webstore.clickerz-bar-builder');
Route::get('/collections', LivewireSytatsu\Webstore\CollectionsPage::class)->name('sytatsu.webstore.collections');
Route::get('/collections/{collection}', LivewireSytatsu\Webstore\CollectionPage::class)->name('sytatsu.webstore.collection');


Route::middleware('has.cart')->group(function () {
    Route::get('/cart', LivewireSytatsu\Webstore\CartPage::class)->name('sytatsu.webstore.cart');
    Route::get('/checkout/success', LivewireSytatsu\Webstore\CheckoutSuccessPage::class)->name('sytatsu.webstore.checkout.success');
    Route::get('/checkout', LivewireSytatsu\Webstore\CheckoutPage::class)->name('sytatsu.webstore.checkout');
});


Route::model('product', Product::class, function (string $slug) {
    return Url::query()
        ->where('slug', $slug)
        ->whereIn('element_type', [
            (new Product)->getMorphClass(),
            Product::class,
            'product',
        ])
        ->orderBy('default', 'desc')
        ->orderBy('id', 'desc')
        ->firstOrFail()
        ->element;
});

Route::model('collection', Collection::class, function (string $slug) {
    return Url::query()
        ->where('slug', $slug)
        ->whereIn('element_type', [
            (new Collection)->getMorphClass(),
            Collection::class,
            'collection',
        ])
        ->orderBy('default', 'desc')
        ->orderBy('id', 'desc')
        ->firstOrFail()
        ->element;
});
