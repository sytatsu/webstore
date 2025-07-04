<?php

use App\Http\Livewire\Sytatsu\Pages as LivewireSytatsu;
use Illuminate\Support\Facades\Route;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use Lunar\Models\Url;

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

Route::get('/', LivewireSytatsu\Welcome::class)->name('sytatsu.welcome');
Route::get('/about', LivewireSytatsu\About::class)->name('sytatsu.about');
Route::get('/contact', LivewireSytatsu\Contact::class)->name('sytatsu.contact');

Route::get('/webstore', LivewireSytatsu\Webstore\Welcome::class)->name('sytatsu.webstore.welcome');
Route::get('/products/{product}', LivewireSytatsu\Webstore\ProductPage::class)->name('sytatsu.webstore.product');
Route::get('/collections/{collection}', LivewireSytatsu\Webstore\CollectionPage::class)->name('sytatsu.webstore.collection');


Route::model('product', Product::class, function (string $slug) {
    return ($element = Url::query()->where('slug', $slug)->firstOrFail()->element) instanceof Product ? $element : abort(404, 'Element not Product');
});

Route::model('collection', Collection::class, function (string $slug) {
    return ($element = Url::query()->where('slug', $slug)->firstOrFail()->element) instanceof Collection ? $element : abort(404, 'Element not Collection');
});
