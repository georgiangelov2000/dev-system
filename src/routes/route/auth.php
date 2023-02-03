<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController as AuthController;

Route::get('/', [AuthController::class, 'home'])->name('/');

Route::post('/register', [AuthController::class, 'postRegister'])->name('post.register');

Route::post('/login', [AuthController::class, 'postLogin'])->name('post.login');

