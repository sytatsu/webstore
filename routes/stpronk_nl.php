<?php

use App\Http\Controllers\Stpronk;
use App\Http\Livewire\Stpronk\Pages as LivewireStpronk;
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

Route::domain(env('APP_STPRONK_URL'))->group(function () {
    Route::get('/', [Stpronk\WebController::class, 'index'])->name('stpronk.index');
    Route::get('/new', LivewireStpronk\Welcome::class)->name('stpronk.welcome');
    Route::get('/login', [Stpronk\WebController::class, 'login'])->name('stpronk.login');
});
