<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController as CategoryApiController;
use App\Http\Controllers\Api\BrandApiController as BrandApiController;
use App\Http\Controllers\Api\SupplierApiController as SupplierApiController;
use App\Http\Controllers\Api\PurchaseApiController as PurchaseApiController;
use App\Http\Controllers\Api\CustomerApiController as CustomerApiController;
use App\Http\Controllers\Api\OrderApiController as OrderApiController;
use App\Http\Controllers\Api\PackageApiController as PackageApiController;
use App\Http\Controllers\Api\SubCategoryApiController as SubCategoryApiController;
use App\Http\Controllers\Api\PurchasePaymentApiController as PurchasePaymentApiController;
use App\Http\Controllers\Api\OrderPaymentApiController as OrderPaymentApiController;
use App\Http\Controllers\Api\InvoiceOrderApiController as InvoiceOrderApiController;
use App\Http\Controllers\Api\InvoicePurchaseApiController as InvoicePurchaseApiController;


Route::middleware(['auth'])->group(function () {
    Route::get('/api/categories', [CategoryApiController::class, 'getData'])->name('api.categories');
    Route::get('/api/brands', [BrandApiController::class, 'getData'])->name('api.brands');
    Route::get('/api/suppliers', [SupplierApiController::class, 'getData'])->name('api.suppliers');
    Route::get('/api/products', [PurchaseApiController::class, 'getData'])->name('api.products');
    Route::get('/api/customers', [CustomerApiController::class, 'getData'])->name('api.customers');
    Route::get('/api/orders', [OrderApiController::class, 'getData'])->name('api.orders');
    Route::get('/api/packages', [PackageApiController::class, 'getData'])->name('api.packages');
    Route::get('/api/subcategories', [SubCategoryApiController::class, 'getData'])->name('api.subcategories');
    Route::get('/api/supplier/payments', [PurchasePaymentApiController::class, 'getData'])->name('api.supplier.payments');
    Route::get('/api/order/payments', [OrderPaymentApiController::class, 'getData'])->name('api.order.payments');
    Route::get('/api/order/invoices', [InvoiceOrderApiController::class, 'getData'])->name('api.order.invoices');
    Route::get('/api/order/purchases', [InvoicePurchaseApiController::class, 'getData'])->name('api.purchase.invoices');

});

