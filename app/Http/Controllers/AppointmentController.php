<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;

class AppointmentController extends Controller
{
    // Fetch artists with the 'tattoo_artist' role
    public function getArtists()
    {
        // Fetch users with the 'tattoo_artist' role
        $artists = User::where('role', 'tattoo_artist')->get(['id', 'name']);
        return response()->json($artists);
    }

    // User books a tattoo appointment
   public function bookAppointment(Request $request)
{
    try {
        // Validate input data
        $validated = $request->validate([
            'artist_id' => 'required|exists:users,id', // Ensure artist exists
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

        // Create the appointment
        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'artist_id' => $validated['artist_id'],
            'appointment_datetime' => $validated['appointment_datetime'],
            'description' => $validated['description'] ?? '',
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Appointment created successfully.',
            'appointment' => $appointment
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'message' => 'Validation error occurred.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        // Handle any other unexpected errors
        return response()->json([
            'message' => 'An unexpected error occurred. Please try again later.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    // User retrieves their appointments
    public function getUserAppointments()
    {
        $appointments = Appointment::where('user_id', auth()->id())->get();
        return response()->json($appointments);
    }

    // Admin retrieves all appointments
    public function getAllAppointments()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'You do not have permission to view all appointments.'], 403);
        }

        $appointments = Appointment::with(['user', 'artist'])->get();
        return response()->json($appointments);
    }

    // Admin updates appointment status
    public function updateAppointmentStatus(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'You do not have permission to update the appointment status.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,canceled',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => $validated['status']]);

        return response()->json(['message' => 'Appointment status updated', 'appointment' => $appointment]);
    }

    // Admin deletes an appointment
    public function deleteAppointment($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'You do not have permission to delete the appointment.'], 403);
        }

        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    // Artist retrieves their assigned appointments
    public function getArtistAppointments()
    {
        if (auth()->user()->role !== 'tattoo_artist') {
            return response()->json(['message' => 'You are not authorized to view artist appointments.'], 403);
        }

        $appointments = Appointment::where('artist_id', auth()->id())->get();
        return response()->json($appointments);
    }
}
