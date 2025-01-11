<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AppointmentController;
use App\Models\User;

// Public routes (no authentication required)
Route::post('/register', [UserAuthController::class, 'register'])->name('register');
Route::post('/login', [UserAuthController::class, 'login'])->name('login');
Route::post('/forgot/password', [UserAuthController::class, 'forgotPassword'])->name('forgotpassword');
Route::post('password/reset', [UserAuthController::class, 'resetPassword']);
// In routes/api.php



// Authenticated routes (only accessible by authenticated users)
Route::middleware('auth:sanctum')->group(function () {

    // Appointment routes
    Route::post('/appointments', [AppointmentController::class, 'bookAppointment']); // User books a tattoo appointment
    Route::get('/user/appointments', [AppointmentController::class, 'getUserAppointments']); // User retrieves their appointments
    Route::get('/artist/appointments', [AppointmentController::class, 'getArtistAppointments']); // Artist retrieves their assigned appointments
    Route::get('/artists', [AppointmentController::class, 'getArtists']);

    // Admin routes (accessible only by admins)
    Route::middleware('can:isAdmin')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'getAllAppointments']); // Admin retrieves all appointments
    Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateAppointmentStatus']); // Admin updates appointment status
    Route::delete('/appointments/{id}', [AppointmentController::class, 'deleteAppointment']); // Admin deletes an appointment
    Route::get('/users', [UserAuthController::class, 'users']); // Fetch users (admin)
    Route::post('/users', [UserAuthController::class, 'createUser']); // Create new user (admin)
    Route::put('/users/{id}', [UserAuthController::class, 'updateUser']); // Update user (admin)
    Route::delete('/users/{id}', [UserAuthController::class, 'deleteUser']); // Delete user (admin)
    });
});
