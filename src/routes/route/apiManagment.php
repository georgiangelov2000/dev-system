<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController as CategoryApiController;
use App\Http\Controllers\Api\BrandApiController as BrandApiController;
use App\Http\Controllers\Api\CountryApiController as CountryApiController;
use App\Http\Controllers\Api\SupplierApiController as SupplierApiController;
use App\Http\Controllers\Api\PurchaseApiController as PurchaseApiController;
use App\Http\Controllers\Api\CustomerApiController as CustomerApiController;
use App\Http\Controllers\Api\OrderApiController as OrderApiController;
use App\Http\Controllers\Api\PackageApiController as PackageApiController;
use App\Http\Controllers\Api\SubCategoryApiController as SubCategoryApiController;
use App\Http\Controllers\Api\PaymentApiController as PaymentApiController;
use App\Http\Controllers\Api\UserApiController as UserApiController;
use App\Http\Controllers\Api\StateApiController as StateApiController;
use App\Http\Controllers\Api\InvoiceApiController as InvoiceApiController;
use App\Http\Controllers\Api\RolesManagementApiController as RolesManagementApiController;

Route::middleware(['auth'])->group(function () {
    Route::get('/api/categories', [CategoryApiController::class, 'getData'])->name('api.categories');
    Route::get('/api/brands', [BrandApiController::class, 'getData'])->name('api.brands');
    Route::get('/api/suppliers', [SupplierApiController::class, 'getData'])->name('api.suppliers');
    Route::get('/api/products', [PurchaseApiController::class, 'getData'])->name('api.products');
    Route::get('/api/customers', [CustomerApiController::class, 'getData'])->name('api.customers');
    Route::get('/api/orders', [OrderApiController::class, 'getData'])->name('api.orders');
    Route::get('/api/packages', [PackageApiController::class, 'getData'])->name('api.packages');
    Route::get('/api/subcategories', [SubCategoryApiController::class, 'getData'])->name('api.subcategories');
    Route::get('/api/payments', [PaymentApiController::class, 'getData'])->name('api.payments');
    Route::get('/api//invoices', [InvoiceApiController::class, 'getData'])->name('api.invoices');
    Route::get('/api/users', [UserApiController::class, 'getData'])->name('api.users');
    Route::get('/api/location', [StateApiController::class, 'getData'])->name('api.location');
    Route::get('/api/countries', [CountryApiController::class, 'getData'])->name('api.countries');    
    Route::get('/api/roles/management', [RolesManagementApiController::class, 'getData'])->name('api.roles.management');

});

