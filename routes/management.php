<?php

use App\Http\Controllers\Auth\AccountDisabledController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Catalog\BrandController;
use App\Http\Controllers\Catalog\CategoryController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\Catalog\ProductVariantController;
use App\Http\Controllers\Catalog\VariantController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Management\ProfileManagementController;
use App\Http\Controllers\ProfileController;
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

                Route::prefix('catalog')->group(function () {
                    Route::get('/' , [CatalogController::class, 'index'])->name('catalog.index');

                    Route::prefix('products')->group(function () {
                        Route::get('/', [ProductController::class, 'list'])->name('catalog.products.list');
                        Route::get('/create', [ProductController::class, 'create'])->name('catalog.products.create');
                        Route::put('/create', [ProductController::class, 'store'])->name('catalog.products.store');
                        Route::get('/{product}', [ProductController::class, 'show'])->name('catalog.products.show');
                        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('catalog.products.edit');
                        Route::patch('/update', [ProductController::class, 'update'])->name('catalog.products.update');
                        Route::post('/discontinue', [ProductController::class, 'discontinue'])->name('catalog.products.discontinue');
                        Route::post('/continue', [ProductController::class, 'continue'])->name('catalog.products.continue');
                        Route::delete('/delete', [ProductController::class, 'delete'])->name('catalog.products.delete');

                        Route::prefix('/{product}/variants')->group(function () {
                            Route::get('/create', [ProductVariantController::class, 'create'])->name('catalog.products.variants.create');
                            Route::put('/create', [ProductVariantController::class, 'store'])->name('catalog.products.variants.store');
                            Route::get('/{productVariant}', [ProductVariantController::class, 'show'])->name('catalog.products.variants.show');
                            Route::get('/{productVariant}/edit', [ProductVariantController::class, 'edit'])->name('catalog.products.variants.edit');
                            Route::patch('/update', [ProductVariantController::class, 'update'])->name('catalog.products.variants.update');
                            Route::delete('/delete', [ProductVariantController::class, 'delete'])->name('catalog.products.variants.delete');
                        });
                    });

                    Route::prefix('brands')->group(function () {
                        Route::get('/', [BrandController::class, 'list'])->name('catalog.brands.list');
                        Route::get('/create', [BrandController::class, 'create'])->name('catalog.brands.create');
                        Route::put('/create', [BrandController::class, 'store'])->name('catalog.brands.store');
                        Route::get('/{brand}', [BrandController::class, 'show'])->name('catalog.brands.show');
                        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('catalog.brands.edit');
                        Route::patch('/update', [BrandController::class, 'update'])->name('catalog.brands.update');
                        Route::delete('/delete', [BrandController::class, 'delete'])->name('catalog.brands.delete');
                    });

                    Route::prefix('categories')->group(function () {
                        Route::get('/', [CategoryController::class, 'list'])->name('catalog.categories.list');
                        Route::get('/create', [CategoryController::class, 'create'])->name('catalog.categories.create');
                        Route::put('/create', [CategoryController::class, 'store'])->name('catalog.categories.store');
                        Route::get('/{category}', [CategoryController::class, 'show'])->name('catalog.categories.show');
                        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('catalog.categories.edit');
                        Route::patch('/update', [CategoryController::class, 'update'])->name('catalog.categories.update');
                        Route::delete('/delete', [CategoryController::class, 'delete'])->name('catalog.categories.delete');
                    });

                    Route::prefix('variants')->group(function () {
                        Route::get('/', [VariantController::class, 'list'])->name('catalog.variants.list');
                        Route::get('/create', [VariantController::class, 'create'])->name('catalog.variants.create');
                        Route::put('/create', [VariantController::class, 'store'])->name('catalog.variants.store');
                        Route::get('/{variant}', [VariantController::class, 'show'])->name('catalog.variants.show');
                        Route::get('/{variant}/edit', [VariantController::class, 'edit'])->name('catalog.variants.edit');
                        Route::patch('/update', [VariantController::class, 'update'])->name('catalog.variants.update');
                        Route::delete('/delete', [VariantController::class, 'delete'])->name('catalog.variants.delete');
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
