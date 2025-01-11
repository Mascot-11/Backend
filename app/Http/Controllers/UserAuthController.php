<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use App\Notifications\CustomResetPasswordNotification;
use Carbon\Carbon;

class UserAuthController extends Controller
{

    // Login method
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // Email is required and must be a valid email
            'password' => 'required|string', // Password is required and must be a string
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt to find the user by email
        $user = User::where('email', $request->email)->first();

        // If user doesn't exist or password doesn't match
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials, Please Try Again'
            ], 401); // Unauthorized
        }

        // Generate a new personal access token using Sanctum with expiry time of 24 hours
        $token = $user->createToken('auth_token', ['*'], Carbon::now()->addHours(24))->plainTextToken;

        // Return response with the generated token and user details
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user, // Include user details
            'role_message' => $this->getRoleMessage($user->role), // Add role-specific message
        ]);
    }

    // Helper function to generate a role-specific message
    private function getRoleMessage($role)
    {
        switch ($role) {
            case 'admin':
                return 'Admin login successful';
            case 'tattoo_artist':
                return 'Tattoo artist login successful';
            default:
                return 'User login successful';
        }
    }

    // Register method
    public function register(Request $request)
    {
        // Validate input, including password confirmation and role
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:tattoo_artist,user', // Ensure role is specified and valid
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new user and hash the password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before saving
            'role' => $request->role, // Store the role
        ]);

        // Generate a new personal access token using Sanctum with expiry time of 24 hours
        $token = $user->createToken('auth_token', ['*'], Carbon::now()->addHours(24))->plainTextToken;

        // Return response with the generated token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Signup successful'
        ]);
    }

    // Forgot Password method
    public function forgotPassword(Request $request)
    {
        // Validate the email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Generate a password reset token
            $token = Password::createToken($user);

            // Send custom password reset notification with the token
            $user->notify(new CustomResetPasswordNotification($user, $token));

            return response()->json([
                'message' => 'Password reset link sent to your email address.'
            ], 200);
        }

        return response()->json([
            'message' => 'Unable to send reset link.'
        ], 500);
    }

    // Reset Password method
    public function resetPassword(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string', // Validate token
            'password' => 'required|string|min:8|confirmed', // Validate password
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt to reset the password using the token
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        // Return response based on reset result
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password successfully reset.'], 200);
        }

        return response()->json(['message' => 'Failed to reset password.'], 500);
    }

    // Get all users (e.g., for admin purposes)
    public function users()
    {
        // Only admins can fetch the list of all users
        if (!in_array(auth()->user()->role, ['tattoo_artist', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }



        $users = User::all();
        return response()->json($users);
    }

    // Create new user (e.g., admin creating users)
    public function createUser(Request $request)
    {
        // Only admin can create users
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,tattoo_artist,user', // Ensure valid role
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']), // Hash the password
            'role' => $validated['role'], // Assign role from request
        ]);

        return response()->json($user, 201); // Return the created user
    }

    // Update user (e.g., for admin purposes)
    public function updateUser(Request $request, $id)
    {
        // Only admins can update users
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->update([
            'name' => $validated['name'] ?? $user->name,
            'email' => $validated['email'] ?? $user->email,
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : $user->password,
        ]);

        return response()->json($user);
    }

    // Delete user (e.g., for admin purposes)
    public function deleteUser($id)
    {
        // Only admins can delete users
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
