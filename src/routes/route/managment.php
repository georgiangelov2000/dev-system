<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
    });

    Route::prefix('dashboard')->name('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });

    Route::prefix('country')->name('country.')->group(function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
    });

    Route::prefix('imports')->name('import.')->group(function () {
        Route::get('/{type}', [ImportController::class, 'index'])->name('index');
        Route::post('/store', [ImportController::class, 'store'])->name('store');
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
        Route::delete('/delete/image/{category}', [CategoryController::class, 'deleteImage'])->name('delete.image');
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
        Route::delete('/delete/image/{brand}', [BrandController::class, 'deleteImage'])->name('delete.image');
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

    Route::resource('purchases', PurchaseController::class);
    Route::put('purchases/mass/edit', [PurchaseController::class, 'massEditUpdate'])->name('purchases.mass.update');
    Route::get('purchases/orders/{purchase}', [PurchaseController::class, 'orders'])->name('purchases.orders');
    Route::put('purchases/update/status/{purchase}', [PurchaseController::class, 'updateSpecificColumns'])->name('purchases.update.status');


    Route::prefix('customers')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/store', [CustomerController::class, 'store'])->name('store');
        Route::get('/edit/{customer}', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::get('/delete/{customer}', [CustomerController::class, 'delete'])->name('delete');
        Route::get('/mass/edit/orders/{customer}', [CustomerController::class, 'customerOrders'])->name('mass.edit.orders');
    });

    Route::prefix('orders')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::delete('/delete/{order}', [OrderController::class, 'delete'])->name('delete');
        Route::put('/update/{order}', [OrderController::class, 'update'])->name('update');
        Route::post('/mass/edit', [OrderController::class, 'massUpdate'])->name('mass.update');
        Route::get('/edit/{order}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/status/{order}', [OrderController::class, 'updateStatus'])->name('status');
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
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'form'])->name('get');
        Route::put('/update', [SettingsController::class, 'update'])->name('update');
    });

    Route::prefix('payments')->name('payment.')->group(function () {
        Route::get('/{type}', [PaymentController::class, 'index'])->name('index');
        Route::get('/edit/{type}/{payment}', [PaymentController::class, 'edit'])->name('edit');
        Route::put('/update/{type}/{payment}', [PaymentController::class, 'update'])->name('update');
        Route::delete('/delete/{payment}/{type}', [PaymentController::class, 'delete'])->name('delete');        
    });

    Route::prefix('invoices')->name('invoice.')->group(function () {
        Route::put('/{type}/update/{invoice}', [InvoiceController::class, 'update'])->name('update');
    });

}); 
