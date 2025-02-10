<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentConfirmed;
use App\Notifications\AppointmentCanceled;

class AppointmentController extends Controller
{
    // Fetch artists with the 'tattoo_artist' role
    public function getArtists()
    {
        try {
            $artists = User::where('role', 'tattoo_artist')->get(['id', 'name']);

            if ($artists->isEmpty()) {
                return response()->json(['message' => 'No artists found.'], 404);
            }

            return response()->json($artists);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching artists.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // User books a tattoo appointment
   public function bookAppointment(Request $request)
{
    try {
        // Validate input data
        $validated = $request->validate([
            'artist_id' => 'required|exists:users,id',
            'appointment_datetime' => [
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime(date('Y-m-d'))) {
                        $fail('The appointment date cannot be in the past.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        // Check if the appointment already exists for the artist and datetime
        $existingAppointment = Appointment::where('artist_id', $validated['artist_id'])
            ->where('appointment_datetime', $validated['appointment_datetime'])
            ->exists();

        if ($existingAppointment) {
            return response()->json([
                'message' => 'The artist is already booked for this date and time.',
                'errors' => [
                    'artist_id' => ['The selected artist is unavailable for the chosen time.']
                ]
            ], 422);
        }

        // Upload image if provided
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageUrl = $this->uploadImageToImgBB($imageFile);

            if (!$imageUrl) {
                return response()->json([
                    'message' => 'Failed to upload the image. Please try again later.',
                ], 500);
            }
        }

        // Create the appointment
        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'artist_id' => $validated['artist_id'],
            'appointment_datetime' => $validated['appointment_datetime'],
            'description' => $validated['description'] ?? '',
            'image_url' => $imageUrl, // Save the image URL
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Appointment created successfully.',
            'appointment' => $appointment
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation error occurred.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An unexpected error occurred. Please try again later.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

private function uploadImageToImgBB($imageFile)
{
    // Get the API key from the .env file
    $apiKey = env('IMGBB_API_KEY');
    $url = 'https://api.imgbb.com/1/upload?key=' . $apiKey;

    // Send the image as multipart/form-data (no base64 encoding)
    $response = Http::attach('image', file_get_contents($imageFile), $imageFile->getClientOriginalName())
        ->post($url);

    // Decode the response JSON
    $data = $response->json();

    // Check if the upload was successful
    if ($response->successful() && isset($data['data']['url'])) {
        return $data['data']['url']; // Return the image URL from ImgBB
    }

    // Log the error if upload fails
    \Log::error('ImgBB image upload failed: ' . $response->body());

    return null; // Return null if image upload failed
}


    // User retrieves their appointments
public function getUserAppointments()
{
    try {
        // Fetching appointments for the authenticated user
        $appointments = Appointment::where('user_id', auth()->id())
            ->with('artist') // Eager loading artist data
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found for this user.'], 404);
        }

        // Mapping each appointment to include artist name and image URL
        $appointments = $appointments->map(function ($appointment) {
            // Add artist name to each appointment
            $appointment->artist_name = $appointment->artist ? $appointment->artist->name : 'Unknown Artist';

            // Directly use the `image_url` stored in the database for the appointment
            $appointment->image_url = $appointment->image_url ? $appointment->image_url : null;

            return $appointment;
        });

        // Return the appointments as JSON response
        return response()->json($appointments);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while fetching user appointments.',
            'error' => $e->getMessage(),
        ], 500);
    }
}




    // Admin retrieves all appointments
    public function getAllAppointments()
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'You do not have permission to view all appointments.'], 403);
            }

            $appointments = Appointment::with(['user', 'artist'])->get();

            if ($appointments->isEmpty()) {
                return response()->json(['message' => 'No appointments found.'], 404);
            }

            return response()->json($appointments);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching all appointments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Admin updates appointment status
    public function updateAppointmentStatus(Request $request, $id)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'You do not have permission to update the appointment status.'], 403);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,canceled',
            ]);

            $appointment = Appointment::find($id);

            if (!$appointment) {
                return response()->json(['message' => 'Appointment not found.'], 404);
            }

            if($validated['status'] === 'confirmed') {
                // Notify the user that their appointment has been confirmed
                $appointment->user->notify(new AppointmentConfirmed($appointment));
            }
            elseif($validated['status'] === 'canceled') {
                // Notify the user that their appointment has been canceled
                $appointment->user->notify(new AppointmentCanceled($appointment));
            }

            $appointment->update(['status' => $validated['status']]);

            return response()->json(['message' => 'Appointment status updated', 'appointment' => $appointment]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the appointment status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Admin deletes an appointment
    public function deleteAppointment($id)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'You do not have permission to delete the appointment.'], 403);
            }

            $appointment = Appointment::find($id);

            if (!$appointment) {
                return response()->json(['message' => 'Appointment not found.'], 404);
            }

            $appointment->delete();

            return response()->json(['message' => 'Appointment deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the appointment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Artist retrieves their assigned appointments
    public function getArtistAppointments()
    {
        try {
            if (auth()->user()->role !== 'tattoo_artist') {
                return response()->json(['message' => 'You are not authorized to view artist appointments.'], 403);
            }

            $appointments = Appointment::where('artist_id', auth()->id())->get();

            if ($appointments->isEmpty()) {
                return response()->json(['message' => 'No appointments found for this artist.'], 404);
            }

            return response()->json($appointments);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching artist appointments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
