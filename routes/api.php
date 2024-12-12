<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [UserAuthController::class, 'register'])->name('register');
Route::post('login', [UserAuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword'])->name('forgotpassword');
Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])->name('resetpassword');
