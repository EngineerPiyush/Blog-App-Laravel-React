<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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

            // Upload and get response (Cloudinary facade)
            $uploaded = Cloudinary::upload($filePath, ['folder' => 'temp_images']);

            // $uploaded may be an object (->getSecurePath()) or an array (['secure_url'])
            $url = null;
            if (is_object($uploaded) && method_exists($uploaded, 'getSecurePath')) {
                $url = $uploaded->getSecurePath();
            } elseif (is_array($uploaded) && isset($uploaded['secure_url'])) {
                $url = $uploaded['secure_url'];
            } elseif (is_object($uploaded) && isset($uploaded->secure_url)) {
                $url = $uploaded->secure_url;
            }

            if (! $url) {
                Log::error('TempImageController: Cloudinary returned no URL', ['uploaded' => $uploaded]);
                return response()->json([
                    'status' => false,
                    'message' => 'Upload succeeded but no URL returned from Cloudinary',
                    'uploaded' => $uploaded
                ], 500);
            }

            $tempImage = new TempImage();
            $tempImage->name = $url; // store full Cloudinary URL
            $tempImage->save();

            return response()->json([
                'status' => true,
                'message' => 'Image Uploaded Successfully',
                'image' => $tempImage
            ]);
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
