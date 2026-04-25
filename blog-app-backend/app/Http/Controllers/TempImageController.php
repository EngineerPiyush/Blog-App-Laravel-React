<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TempImageController extends Controller
{
    public function store(Request $request)
    {
        // Apply Validation
        $validator = Validator::make($request->all(), [
            'image' => 'required|image' // tightened rule (no spaces around pipe)
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Quick check: ensure file exists and is valid
        if (! $request->hasFile('image') || ! $request->file('image')->isValid()) {
            Log::error('TempImageController: no file or invalid file', [
                'hasFile' => $request->hasFile('image'),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'No file uploaded or file is invalid'
            ], 422);
        }

        // --- OLD Local Upload Code (kept for future local dev)
        /*
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $tempImage = new TempImage();
        $tempImage->name = $imageName;
        $tempImage->save();
        $image->move(public_path('uploads/temp/'), $imageName);
        return response()->json([
            'status' => true,
            'message' => 'Image Uploaded Successfully',
            'image' => $tempImage
        ]);
        */

        // --- CLOUDINARY UPLOAD (active) ---
        try {
    $filePath = $request->file('image')->getRealPath();

    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => 'dct1mmp4t',
            'api_key'    => '336521794433257',
            'api_secret' => 'eYN-xmf6PsaBdFY9sAHIaL4K5Yc',
        ],
    ]);

    $uploaded = $cloudinary->uploadApi()->upload($filePath);

    $url = $uploaded['secure_url'];

    $tempImage = new TempImage();
    $tempImage->name = $url;
    $tempImage->save();

    return response()->json([
        'status' => true,
        'message' => 'Image Uploaded Successfully',
        'image' => $tempImage
    ]);

} catch (\Exception $e) {
    Log::error('Cloudinary upload failed: ' . $e->getMessage());

    return response()->json([
        'status' => false,
        'message' => 'Upload failed',
        'error' => $e->getMessage()
    ], 500);
} catch (\Exception $e) {
            Log::error('TempImageController: Cloudinary upload failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Upload failed (server).',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
