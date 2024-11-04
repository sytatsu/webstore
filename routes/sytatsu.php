<?php

use App\Http\Controllers\Auth\AccountDisabledController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Management\ProfileManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StpronkController;
use App\Http\Controllers\Warehouse\BrandController;
use App\Http\Controllers\Warehouse\CategoryController;
use App\Http\Controllers\Warehouse\ProductController;
use App\Http\Controllers\Warehouse\VariantController;
use App\Http\Controllers\WarehouseController;
use App\Models\User;
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

Route::domain(env('APP_SYTATSU_URL'))->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/account-disabled', [AccountDisabledController::class, 'index'])->name('account-disabled');

        Route::middleware('not.disabled')->group(function () {
            Route::get('/password-update', [NewPasswordController::class, 'create'])->name('new-password-update.index');
            Route::post('/password-update', [NewPasswordController::class, 'store'])->name('new-password-update.store');

            Route::middleware(['verified', 'password.updated'])->group(function (){
                Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

                Route::prefix('warehouse')->group(function () {
                    Route::get('/' , [WarehouseController::class, 'index'])->name('warehouse.index');

                    Route::prefix('products')->group(function () {
                        Route::get('/', [ProductController::class, 'list'])->name('warehouse.products.list');
                        Route::get('/create', [ProductController::class, 'create'])->name('warehouse.products.create');
                    });

                    Route::prefix('brands')->group(function () {
                        Route::get('/', [BrandController::class, 'list'])->name('warehouse.brands.list');
                        Route::get('/create', [BrandController::class, 'create'])->name('warehouse.brands.create');
                    });

                    Route::prefix('variants')->group(function () {
                        Route::get('/', [VariantController::class, 'list'])->name('warehouse.variants.list');
                        Route::get('/create', [VariantController::class, 'create'])->name('warehouse.variants.create');
                    });

                    Route::prefix('categories')->group(function () {
                        Route::get('/', [CategoryController::class, 'list'])->name('warehouse.categories.list');
                        Route::get('/create', [CategoryController::class, 'create'])->name('warehouse.categories.create');
                    });
                });

                Route::prefix('profile')->group(function () {
                    Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
                    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
                    Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
                });

                Route::middleware('is.admin')->group(function () {
                    Route::model('user', User::class);

                    Route::get('/admin/management/profile', [ProfileManagementController::class, 'index'])->name('admin.management.profile.index');
                    Route::post('/admin/management/profile/{user}/disable', [ProfileManagementController::class, 'disable'])->name('admin.management.profile.disable');
                    Route::patch('/admin/management/profile/{user}/enable', [ProfileManagementController::class, 'enable'])->name('admin.management.profile.enable');
                    Route::delete('/admin/management/profile/{user}/delete', [ProfileManagementController::class, 'delete'])->name('admin.management.profile.delete');

                    Route::get('/admin/management/profile/create', [ProfileManagementController::class, 'create'])->name('admin.management.profile.create');
                    Route::put('/admin/management/profile/create', [ProfileManagementController::class, 'store'])->name('admin.management.profile.store');
                });
            });
        });
    });
});
