<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        $result = [
            'ip' => $_SERVER['REMOTE_ADDR'],
        ];

        return response()->json($result);
    }

    /**
     * Handle the file upload request.
     *
     * This method validates the incoming request to ensure it contains a valid image
     * and checks if the request is coming from an allowed IP address. If validation passes,
     * the image is saved in the 'private' folder and the local path is returned.
     *
     * @param Request $request The incoming HTTP request that should contain the file.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function uploadImage(Request $request)
    {
        $allowedIps = explode(',', config('app.allow_ips'));
        // Get client IP address
        $clientIp = $request->ip();
        // Check if the client IP is in the allowed list
        if (! in_array($clientIp, $allowedIps)) {
            return response()->json(['error' => 'Access denied from this IP address'], 403);
        }

        // Validate the uploaded file (must be an image with max size of 5MB)
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:5120|mimes:jpg,jpeg,png,gif',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Get the uploaded file
        $file = $request->file('image');

        // Generate a unique filename for the image
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

        // Store the file in the 'private' directory under 'storage/app/private'
        //$path = $file->storeAs('private', $filename);
        // Store the file on the private disk
        //$path = Storage::disk('private')->putFileAs('', $file, $fileName);
        $path = Storage::disk('private')->put("upload-images/{$filename}", file_get_contents($file));

        // Get the full local path of the saved file
        $localPath = storage_path('app/' . "private/upload-images/{$filename}");

        // Return a success response with the local path of the image
        return response()->json(['message' => 'Image uploaded successfully', 'path' => $localPath], 201);
    }
}
