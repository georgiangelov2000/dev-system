<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController as AuthController;

Route::get('/', function () {
    return view('app');
});

Route::prefix('register')->group(function () {
    Route::get('/', [AuthController::class, 'getRegister'])->name('register');
    Route::post('/post', [AuthController::class, 'postRegister'])->name('post.register');
});

Route::prefix('login')->group(function () {
    Route::get('/', [AuthController::class, 'getLogin'])->name('login');
    Route::post('/post', [AuthController::class, 'postLogin'])->name('post.login');
});
