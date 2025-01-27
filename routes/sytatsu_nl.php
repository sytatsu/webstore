<?php

use App\Http\Livewire\Sytatsu\Pages as LivewireSytatsu;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes | https://stpronk.nl/
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::domain(env('APP_SYTATSU_URL'))->group(function () {
    Route::get('/', LivewireSytatsu\Welcome::class)->name('sytatsu.welcome');
    Route::get('/on-demand', LivewireSytatsu\OnDemand::class)->name('sytatsu.on-demand');
});
