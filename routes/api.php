<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Notifications\CustomResetPasswordNotification;

Route::middleware('auth:sanctum')->group(function () {

    // Admin and authorized users only
    Route::get('/users', [UserAuthController::class, 'users']); // Fetch users (admin)
    Route::post('/users', [UserAuthController::class, 'createUser']); // Create new user (admin)
    Route::put('/users/{id}', [UserAuthController::class, 'updateUser']); // Update user (admin)
    Route::delete('/users/{id}', [UserAuthController::class, 'deleteUser']); // Delete user (admin)

});

// Public routes (no authentication required)
Route::post('/register', [UserAuthController::class, 'register'])->name('register');
Route::post('/login', [UserAuthController::class, 'login'])->name('login');
Route::post('/forgot/password', [UserAuthController::class, 'forgotPassword'])->name('forgotpassword');
Route::post('password/reset', [UserAuthController::class, 'resetPassword']);
