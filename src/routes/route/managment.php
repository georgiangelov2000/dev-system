<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('categories')->name('category.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/update/{category}', [CategoryController::class, 'update'])->name('update');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/delete/{category}', [CategoryController::class, 'delete'])->name('delete');
         Route::get('/detach/subcategory/{subcategory}', [CategoryController::class, 'detachSubCategory'])->name('detach.subcategory');
    });

    Route::prefix('brands')->name('brand.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/edit/{brand}', [BrandController::class, 'edit'])->name('edit');
        Route::post('/update/{brand}', [BrandController::class, 'update'])->name('update');
        Route::post('/store', [BrandController::class, 'store'])->name('store');
        Route::get('/delete/{brand}', [BrandController::class, 'delete'])->name('delete');
    });

    Route::prefix('suppliers')->name('supplier.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store');
        Route::get('/edit/{supplier}', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/update/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::get('/delete/{supplier}', [SupplierController::class, 'delete'])->name('delete');
        Route::get('/state/{countryId}', [SupplierController::class, 'getState'])->name('state');
        Route::get('/detach/category/{category}', [SupplierController::class, 'detachCategory'])->name('detach.category');
    });
    
    Route::prefix('products')->name('product.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::get('/edit/{product}', [ProductController::class, 'edit'])->name('edit');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::put('/update/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/delete/{product}', [ProductController::class, 'delete'])->name('delete');
    });
    
    
    Route::prefix('states')->name('state')->group(function(){
        Route::get('/state/{countryId}', [SupplierController::class, 'getState']);
    });
});