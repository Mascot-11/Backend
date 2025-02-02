<?php

namespace App\Http\Controllers;

use App\Models\TattooGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class TattooGalleryController extends Controller
{
    // No need for authentication middleware for viewing gallery images
    public function __construct()
    {
        // We are not restricting access to index method; only store and destroy are restricted to admin
    }

    // Fetch all tattoo gallery images for public view
    public function index()
    {
        $images = TattooGallery::all(); // Fetch all images with descriptions
        return response()->json($images);
    }

    // Admin upload a new image with description
    public function store(Request $request)
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403); // Not an admin
        }

        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // max size 2MB
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Handle image upload via ImgBB API
        $imagePath = $this->uploadImageToImgBB($request->file('image'));

        if ($imagePath === '') {
            return response()->json(['message' => 'Image upload failed'], 500);
        }

        // Store image in the tattoo gallery
        $tattooGallery = TattooGallery::create([
            'image_url' => $imagePath,
            'description' => $request->description,
        ]);

        return response()->json($tattooGallery, 201);
    }

    // Admin delete an image
    public function destroy($id)
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403); // Not an admin
        }

        $tattooGallery = TattooGallery::find($id);

        if (!$tattooGallery) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // Delete the image
        $tattooGallery->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }

    // Method to upload image to ImgBB
    private function uploadImageToImgBB($image)
    {
        $apiKey = env('IMGBB_API_KEY'); // Store your ImgBB API Key in the .env file
        \Log::info('App Name: ' . env('APP_NAME'));

        // Log the API key for debugging purposes
        \Log::info('ImgBB API Key: ' . $apiKey);

        // Ensure the ImgBB API Key is set
        if (!$apiKey) {
            \Log::error('ImgBB API key not found.');
            return '';
        }

        // Get image data
        $imageData = file_get_contents($image->getRealPath());

        // Upload the image and return the URL
        return $this->uploadToImgBBApi($imageData, $apiKey);
    }

    // Method to send image to ImgBB API
    private function uploadToImgBBApi($imageData, $apiKey)
    {
        $url = 'https://api.imgbb.com/1/upload';

        // Prepare the data for the POST request
        $data = [
            'key' => $apiKey, // API key for ImgBB
            'image' => base64_encode($imageData), // Base64-encoded image data
        ];

        try {
            // Send POST request to ImgBB API
            $response = Http::asForm()->post($url, $data); // Sending data as form-data
            $responseData = json_decode($response->getBody()->getContents(), true);

            // Log full response for debugging


            // Check if the image was uploaded successfully
            if ($response->successful() && isset($responseData['data']['url'])) {
                return $responseData['data']['url']; // Return image URL if successful
            }

            // Log error if upload fails

        } catch (\Exception $e) {
            // Log any exceptions that occur during the upload

        }
    }
}
