<?php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TattooGalleryController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\EventController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tickets/purchase', [TicketController::class, 'purchase']);
    Route::post('/tickets/verify-esewa', [TicketController::class, 'verifyEsewaPayment']);
    Route::get('/ticket-details/{ticketId}', [TicketController::class, 'getTicketDetails']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    });

// Public route to fetch all tattoo gallery images
Route::get('/tattoo-gallery', [TattooGalleryController::class, 'index']); // Public route for viewing gallery

Route::middleware('auth:api')->group(function () {
    // Admin-only routes for adding and deleting images
    Route::post('/tattoo-gallery', [TattooGalleryController::class, 'store']); // Admin upload image
    Route::delete('/tattoo-gallery/{id}', [TattooGalleryController::class, 'destroy']); // Admin delete image
});


// Public routes (no authentication required)
Route::post('/register', [UserAuthController::class, 'register'])->name('register');
Route::post('/login', [UserAuthController::class, 'login'])->name('login');
Route::post('/forgot/password', [UserAuthController::class, 'forgotPassword'])->name('forgotpassword');
Route::post('password/reset', [UserAuthController::class, 'resetPassword']);
Route::get('/artists', [AppointmentController::class, 'getArtists']);
// In routes/api.php



// Authenticated routes (only accessible by authenticated users)
Route::middleware('auth:sanctum')->group(function () {

    // Appointment routes
    Route::post('/appointments', [AppointmentController::class, 'bookAppointment']); // User books a tattoo appointment
    Route::get('/user/appointments', [AppointmentController::class, 'getUserAppointments']); // User retrieves their appointments
    Route::get('/artist/appointments', [AppointmentController::class, 'getArtistAppointments']); // Artist retrieves their assigned appointments


    // Admin routes (accessible only by admins)
    Route::middleware('can:isAdmin')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'getAllAppointments']); // Admin retrieves all appointments
    Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateAppointmentStatus']); // Admin updates appointment status
    Route::delete('/appointments/{id}', [AppointmentController::class, 'deleteAppointment']); // Admin deletes an appointment
    Route::get('/users', [UserAuthController::class, 'users']); // Fetch users (admin)
    Route::post('/users', [UserAuthController::class, 'createUser']); // Create new user (admin)
    Route::put('/users/{id}', [UserAuthController::class, 'updateUser']); // Update user (admin)
    Route::delete('/users/{id}', [UserAuthController::class, 'deleteUser']); // Delete user (admin)
    Route::post('/tattoo-gallery', [TattooGalleryController::class, 'store']); // Upload new image
    Route::delete('/tattoo-gallery/{id}', [TattooGalleryController::class, 'destroy']); // Delete image
    });



Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/chat/start', [ChatController::class, 'startChat']);
    Route::get('/chats', [ChatController::class, 'listChats'])->middleware('can:isAdmin');
    Route::get('/chat/{chat}/messages', [ChatController::class, 'fetchMessages']);
    Route::post('/chat/{chat}/message', [ChatController::class, 'sendMessage']);
});

});


Route::post('/khalti/callback', [TicketController::class, 'handleKhaltiCallback'])->name('khalti.callback');