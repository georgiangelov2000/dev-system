<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\CustomerSummaryController;
use App\Http\Controllers\SupplierSummaryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('dashboard')->name('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });

    Route::prefix('users')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
        Route::post('/update/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{user}', [UserController::class, 'delete'])->name('delete');
    });

    Route::prefix('categories')->name('category.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/update/{category}', [CategoryController::class, 'update'])->name('update');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::delete('/delete/{category}', [CategoryController::class, 'delete'])->name('delete');
        Route::delete('/detach/subcategory/{subcategory}', [CategoryController::class, 'detachSubCategory'])->name('detach.subcategory');
    });

    Route::prefix('subcategories')->name('subcategory.')->group(function () {
        Route::get('/', [SubCategoryController::class, 'index'])->name('index');
        Route::post('/store', [SubCategoryController::class, 'store'])->name('store');
        Route::delete('/delete/{subcategory}', [SubCategoryController::class, 'delete'])->name('delete');
        Route::get('/edit/{subcategory}', [SubCategoryController::class, 'edit'])->name('edit');
        Route::put('/update/{subcategory}', [SubCategoryController::class, 'update'])->name('update');
    });

    Route::prefix('brands')->name('brand.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/edit/{brand}', [BrandController::class, 'edit'])->name('edit');
        Route::post('/update/{brand}', [BrandController::class, 'update'])->name('update');
        Route::post('/store', [BrandController::class, 'store'])->name('store');
        Route::delete('/delete/{brand}', [BrandController::class, 'delete'])->name('delete');
        Route::delete('/detach/purchase/{brand}', [BrandController::class, 'detachPurchase'])->name('detach.purchase');
        Route::get('/purchases/{brand}', [BrandController::class, 'purchases'])->name('purchases');
    });

    Route::prefix('suppliers')->name('supplier.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store');
        Route::get('/edit/{supplier}', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/update/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::get('/mass/edit/purchases/{supplier}', [SupplierController::class, 'massEdit'])->name('mass.edit.purchases');
        Route::delete('/delete/{supplier}', [SupplierController::class, 'delete'])->name('delete');
        Route::get('/detach/category/{category}', [SupplierController::class, 'detachCategory'])->name('detach.category');
    });

    Route::prefix('purchases')->name('purchase.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseController::class, 'create'])->name('create');
        Route::get('/edit/{purchase}', [PurchaseController::class, 'edit'])->name('edit');
        Route::get('/orders/{purchase}', [PurchaseController::class, 'orders'])->name('orders');
        Route::get('/preview/{purchase}', [PurchaseController::class, 'preview'])->name('preview');
        Route::post('/store', [PurchaseController::class, 'store'])->name('store');
        Route::put('/update/{purchase}', [PurchaseController::class, 'update'])->name('update');
        Route::put('/mass/edit', [PurchaseController::class, 'massEditUpdate'])->name('mass.update');
        Route::delete('/delete/{purchase}', [PurchaseController::class, 'delete'])->name('delete');
        Route::delete('/delete/image/{purchase}', [PurchaseController::class, 'deleteGalleryImage'])->name('delete.image');
    });

    Route::prefix('customers')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/store', [CustomerController::class, 'store'])->name('store');
        Route::get('/edit/{customer}', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::get('/delete/{customer}', [CustomerController::class, 'delete'])->name('delete');
        Route::get('/orders/{customer}', [CustomerController::class, 'customerOrders'])->name('orders');
    });

    Route::prefix('orders')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::delete('/delete/{order}', [OrderController::class, 'delete'])->name('delete');
        Route::put('/update/{order}', [OrderController::class, 'update'])->name('update');
        Route::put('/mass/update', [OrderController::class, 'massUpdate'])->name('mass.update');
        Route::get('/edit/{order}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/status/{order}', [OrderController::class, 'updateStatus'])->name('status');
    });

    Route::prefix('summary')->name('summary.')->group(function () {
        Route::get('/customers', [CustomerSummaryController::class, 'index'])->name('customer');
        Route::get('/suppliers', [SupplierSummaryController::class, 'index'])->name('supplier');
        Route::post('/take/customers', [CustomerSummaryController::class, 'summary'])->name('take.customer');
        Route::post('/take/suppliers', [SupplierSummaryController::class, 'summary'])->name('take.supplier');
    });

    Route::prefix('reports')->name('report.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::post('/take', [ReportsController::class, 'takeReport'])->name('take');
    });

    Route::prefix('packages')->name('package.')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('index');
        Route::get('/create', [PackageController::class, 'create'])->name('create');
        Route::post('/store', [PackageController::class, 'store'])->name('store');
        Route::delete('/delete/{package}', [PackageController::class, 'delete'])->name('delete');
        Route::put('/update/{package}', [PackageController::class, 'update'])->name('update');
        Route::get('/edit/{package}', [PackageController::class, 'edit'])->name('edit');
        Route::put('/status/{package}', [PackageController::class, 'updateSpecificColumns'])->name('status');
        Route::get('/orders/{package}', [PackageController::class, 'orders'])->name('orders');
        Route::get('/payment', [PackageController::class, 'createPayment'])->name('create.customer.payment');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'form'])->name('get');
        Route::put('/update', [SettingsController::class, 'update'])->name('update');
    });

    Route::prefix('payments')->name('payment.')->group(function () {
        Route::get('/customers', [PaymentController::class, 'customerPayments'])->name('customer');
        Route::get('/suppliers', [PaymentController::class, 'supplierPayments'])->name('supplier');

        Route::get('/purchases', [PaymentController::class, 'createPurchasePayment'])->name('purchase');
        Route::get('/purchase/{payment}', [PaymentController::class, 'editPurchasePayment'])->name('purchase.edit');
        Route::post('/store/purchase/payment', [PaymentController::class, 'storePurchasePayment'])->name('store.purchase');
        Route::put('/update/purchase/{payment}', [PaymentController::class, 'updatePurchasePayment'])->name('update.purchase');

        Route::get('/orders', [PaymentController::class, 'createOrderPayment'])->name('orders');
        Route::get('/order/{payment}', [PaymentController::class, 'editOrderPayment'])->name('edit.order');
        Route::post('/store/order/payment', [PaymentController::class, 'storeOrderPayment'])->name('store.order');
        Route::put('/update/order/{payment}', [PaymentController::class, 'updateOrderPayment'])->name('update.order');

    });

    Route::prefix('invoices')->name('invoice.')->group(function () {
        Route::put('/purchase/update/{invoice}', [InvoiceController::class, 'updatePurchaseInvoice'])->name('update.purchase');
        Route::put('/order/update/{invoice}', [InvoiceController::class, 'updateOrderInvoice'])->name('update.order');
    });

    Route::prefix('states')->name('state')->group(function () {
        Route::get('/state/{countryId}', [SupplierController::class, 'getState']);
    });
}); 
