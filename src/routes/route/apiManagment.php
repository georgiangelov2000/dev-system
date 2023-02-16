<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController as CategoryApiController;
use App\Http\Controllers\Api\BrandApiController as BrandApiController;


Route::middleware(['auth'])->group(function () {
    Route::get('/api/categories', [CategoryApiController::class, 'getData'])->name('api.categories');
    Route::get('/api/brands', [BrandApiController::class, 'getData'])->name('api.brands');
});
