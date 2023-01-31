<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController as HomeController;
use App\Http\Controllers\AuthController as AuthController;

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
