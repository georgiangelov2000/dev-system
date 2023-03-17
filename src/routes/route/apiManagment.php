<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController as CategoryApiController;
use App\Http\Controllers\Api\BrandApiController as BrandApiController;
use App\Http\Controllers\Api\SupplierApiController as SupplierApiController;
use App\Http\Controllers\Api\ProductApiController as ProductApiController;

Route::middleware(['auth'])->group(function () {
    Route::get('/api/categories', [CategoryApiController::class, 'getData'])->name('api.categories');
    Route::get('/api/brands', [BrandApiController::class, 'getData'])->name('api.brands');
    Route::get('/api/suppliers', [SupplierApiController::class, 'getData'])->name('api.suppliers');
    Route::get('/api/products', [ProductApiController::class, 'getData'])->name('api.products');
});

