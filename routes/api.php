<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Notifications\CustomResetPasswordNotification;




Route::get('/users', [UserAuthController::class, 'users']);
Route::post('register', [UserAuthController::class, 'register'])->name('register');
Route::post('login', [UserAuthController::class, 'login'])->name('login');
Route::post('/forgot/password', [UserAuthController::class, 'forgotPassword'])->name('forgotpassword');
Route::post('password/reset', [UserAuthController::class, 'resetPassword']);
Route::get('/users', [UserAuthController::class, 'users']);
Route::post('/users', [UserAuthController::class, 'createUser']);
Route::put('/users/{id}', [UserAuthController::class, 'updateUser']);
Route::delete('/users/{id}', [UserAuthController::class, 'deleteUser']);

