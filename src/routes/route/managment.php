<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\CustomerSummaryController;
use App\Http\Controllers\SupplierSummaryController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('dashboard')->name('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
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
    
    Route::prefix('purchases')->name('purchase.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::get('/edit/{product}', [ProductController::class, 'edit'])->name('edit');
        Route::get('/preview/{product}', [ProductController::class, 'preview'])->name('preview');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::put('/update/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/delete/{product}', [ProductController::class, 'delete'])->name('delete');
        Route::delete('/delete/image/{product}', [ProductController::class, 'deleteGalleryImage'])->name('delete.image');
    });

    Route::prefix('customers')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/store', [CustomerController::class, 'store'])->name('store');
        Route::put('/update/orders', [CustomerController::class, 'updateCustomerOrders'])->name('update.orders');
        Route::get('/edit/{customer}', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::get('/delete/{customer}', [CustomerController::class, 'delete'])->name('delete');
        Route::get('/state/{countryId}', [CustomerController::class, 'getState'])->name('state');
        Route::get('/orders/{customer}', [CustomerController::class, 'customerOrders'])->name('orders');
        Route::get('/create/payment', [CustomerController::class, 'createCustomerPayment'])->name('create.payment');
    });
    
    Route::prefix('orders')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::delete('/delete/{order}', [OrderController::class, 'delete'])->name('delete');
        Route::put('/update/{order}', [OrderController::class, 'update'])->name('update');
        Route::get('/edit/{order}', [OrderController::class, 'edit'])->name('edit');
        Route::post('/status/{order}', [OrderController::class, 'updateStatus'])->name('status');
    });

    Route::prefix('summary')->name('summary.')->group(function () {
        Route::get('/customers', [CustomerSummaryController::class, 'index'])->name('customer');
        Route::get('/suppliers', [SupplierSummaryController::class, 'index'])->name('supplier');
        Route::post('/take/customers', [CustomerSummaryController::class, 'summary'])->name('take.customer');
        Route::post('/take/suppliers', [SupplierSummaryController::class, 'summary'])->name('take.supplier');
    });

    Route::prefix('packages')->name('package.')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('index');
        Route::get('/create', [PackageController::class, 'create'])->name('create');
        Route::post('/store', [PackageController::class, 'store'])->name('store');
        Route::delete('/delete/{package}', [PackageController::class, 'delete'])->name('delete');
        Route::put('/update/{package}', [PackageController::class, 'update'])->name('update');
        Route::get('/edit/{package}', [PackageController::class, 'edit'])->name('edit');
        Route::put('/status/{package}', [PackageController::class, 'status'])->name('status');
    });

    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('/company', [SettingsController::class, 'companySettingsForm'])->name('company');
        Route::get('/email', [SettingsController::class, 'emailForm'])->name('email');
        Route::get('/server', [SettingsController::class, 'serverInformation'])->name('server');

        Route::post('/send/email', [SettingsController::class, 'sendEmailForm'])->name('email.send');
        Route::put('/comapny/update', [SettingsController::class, 'updateCompanySettings'])->name('company.update');
    });
    
    Route::prefix('payments')->name('payment.')->group(function(){
        Route::get('/', [CustomerPaymentController::class, 'index'])->name('customer');
        Route::post('/check', [CustomerPaymentController::class, 'payment'])->name('check');
    });

    Route::prefix('states')->name('state')->group(function(){
        Route::get('/state/{countryId}', [SupplierController::class, 'getState']);
    });
});