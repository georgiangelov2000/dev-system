<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController as HomeController;
use App\Http\Controllers\CategoryController as CategoryController;
use App\Http\Controllers\BrandController as BrandController;
use App\Http\Controllers\AuthController as AuthController;

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('category.index');
        Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
        Route::post('/update/{category}', [CategoryController::class, 'update'])->name('category.update');
        Route::get('/delete/{category}', [CategoryController::class, 'delete'])->name('category.delete');
        Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('category.edit');
    });

    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('brand.index');
        Route::post('/store', [BrandController::class, 'store'])->name('brand.store');
        Route::post('/update/{brand}', [BrandController::class, 'update'])->name('brand.update');
        Route::get('/delete/{brand}', [BrandController::class, 'delete'])->name('brand.delete');
        Route::get('/edit/{brand}', [BrandController::class, 'edit'])->name('brand.edit');
    });
});
