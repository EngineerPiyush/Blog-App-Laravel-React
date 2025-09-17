<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Cloudinary\Uploader; // old direct usage (commented, not needed now)
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TempImageController extends Controller
{
    public function store(Request $request)
    {
        // Apply Validation
        $validator = Validator::make($request->all(), [
            'image' => 'required | image'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix errors',
                'errors' => $validator->errors()
            ]);
        }

        // --- OLD Local Upload Code ---
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

        // --- NEW Cloudinary Upload Code ---
        $uploaded = Cloudinary::upload(
            $request->file('image')->getRealPath(),
            ['folder' => 'temp_images'] // optional folder
        );

        $tempImage = new TempImage();
        $tempImage->name = $uploaded->getSecurePath(); // store Cloudinary URL
        $tempImage->save();

        return response()->json([
            'status' => true,
            'message' => 'Image Uploaded Successfully',
            'image' => $tempImage
        ]);
    }
}
