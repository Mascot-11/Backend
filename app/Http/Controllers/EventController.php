<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class EventController extends Controller
{
    // Display all events
    public function index() {
        return response()->json(Event::all());
    }

    // Create a new event with image upload to ImgBB
   public function store(Request $request) {
    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'date' => 'required|date',
        'price' => 'required|numeric|min:0',
        'available_tickets' => 'required|integer|min:1',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',  // Validate image
    ]);

    // Upload the image to ImgBB
    $imageUrl = $this->uploadImageToImgBB($request->file('image'));

    // Create the event
    $event = Event::create([
        'name' => $request->name,
        'description' => $request->description,
        'date' => $request->date,
        'price' => $request->price,
        'available_tickets' => $request->available_tickets,
        'image_url' => $imageUrl,  // Store the image URL
    ]);

    return response()->json($event, 201);
}


    // Show a single event
    public function show($id) {
        return response()->json(Event::findOrFail($id));
    }

    // Update event with image upload to ImgBB
    public function update(Request $request, $id) {
        // Validate the request
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'date' => 'date',
            'price' => 'numeric|min:0',
            'available_tickets' => 'integer|min:1',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',  // Validate image
        ]);

        // Find the event
        $event = Event::findOrFail($id);

        // If image is uploaded, upload it to ImgBB and update the URL
        if ($request->hasFile('image')) {
            $imageUrl = $this->uploadImageToImgBB($request->file('image'));
            $event->image_url = $imageUrl;  // Update image URL
        }

        // Update other fields
        $event->update($request->only(['name', 'description', 'date', 'price', 'available_tickets']));

        return response()->json($event);
    }

    // Delete an event
    public function destroy($id) {
        Event::findOrFail($id)->delete();
        return response()->json(['message' => 'Event deleted']);
    }

    // Helper function to upload image to ImgBB
    private function uploadImageToImgBB($image) {
        $client = new Client();
        $apiKey = env('IMGBB_API_KEY'); // Replace with your ImgBB API Key

        // Prepare the image data
        $imagePath = $image->getRealPath();
        $response = $client->post('https://api.imgbb.com/1/upload', [
            'form_params' => [
                'key' => $apiKey,
                'image' => base64_encode(file_get_contents($imagePath)),
            ],
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);

        // Return the image URL
        return $data['data']['url'];
    }
}
