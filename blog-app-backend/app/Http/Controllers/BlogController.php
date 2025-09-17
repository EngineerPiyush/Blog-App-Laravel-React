<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BlogController extends Controller
{
    // this method return all blogs
    public function index(Request $request)
    {
        $blogs = Blog::orderBy('created_at', 'DESC');

        if (!empty($request->keyword)) {
            $blogs = $blogs->where('title', 'like', '%' . $request->keyword . '%');
        }
        $blogs = $blogs->get();
        return response()->json([
            'status' => true,
            'data' => $blogs
        ]);
    }

    // this method return a single blog
    public function show($id)
    {
        $blog = Blog::find($id);
        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ]);
        }
        $blog['date'] = \Carbon\Carbon::parse($blog->created_at)->format('d M , y');
        return response()->json([
            'status' => true,
            'data' => $blog
        ]);
    }

    // this method inserts a blog
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required | min:10',
            'author' => 'required | min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the errors',
                'errors' => $validator->errors()
            ]);
        }

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->author = $request->author;
        $blog->description = $request->description;
        $blog->shortDesc = $request->shortDesc;
        $blog->save();

        // --- OLD Local Image Save Logic ---
        /*
        $tempImage = TempImage::find($request->image_id);
        if ($tempImage != null) {
            $imageExtArray = explode('.', $tempImage->name);
            $ext = last($imageExtArray);
            $imageName = time() . '-' . $blog->id . '.' . $ext;
            $blog->image = $imageName;
            $blog->save();

            $sourcePath = public_path('uploads/temp/' . $tempImage->name);
            $destPath = public_path('uploads/blogs/' . $imageName);

            if (File::copy($sourcePath, $destPath)) {
                File::delete($sourcePath);
                $tempImage->delete();
            }
        }
        */

        // --- NEW Cloudinary Logic ---
        $tempImage = TempImage::find($request->image_id);
        if ($tempImage != null) {
            $blog->image = $tempImage->name; // already Cloudinary URL
            $blog->save();
            $tempImage->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'blog created successfully',
            'data' => $blog
        ]);
    }

    // this method updates a blog
    public function update($id, Request $request)
    {
        $blog = Blog::find($id);
        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required | min:10',
            'author' => 'required | min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the errors',
                'errors' => $validator->errors()
            ]);
        }

        $blog->title = $request->title;
        $blog->author = $request->author;
        $blog->description = $request->description;
        $blog->shortDesc = $request->shortDesc;
        $blog->save();

        // --- OLD Local Update Logic ---
        /*
        $tempImage = TempImage::find($request->image_id);
        if ($tempImage != null) {
            if ($blog->image && File::exists(public_path('uploads/blogs/' . $blog->image))) {
                File::delete(public_path('uploads/blogs/' . $blog->image));
            }
            $imageExtArray = explode('.', $tempImage->name);
            $ext = last($imageExtArray);
            $imageName = time() . '-' . $blog->id . '.' . $ext;
            $blog->image = $imageName;
            $blog->save();

            $sourcePath = public_path('uploads/temp/' . $tempImage->name);
            $destPath = public_path('uploads/blogs/' . $imageName);

            if (File::copy($sourcePath, $destPath)) {
                File::delete($sourcePath);
                $tempImage->delete();
            }
        }
        */

        // --- NEW Cloudinary Logic ---
        $tempImage = TempImage::find($request->image_id);
        if ($tempImage != null) {
            $blog->image = $tempImage->name; // Cloudinary URL
            $blog->save();
            $tempImage->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'blog updated successfully',
            'data' => $blog
        ]);
    }

    // this method deletes a blog
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found',
            ]);
        }

        // --- OLD Local Delete Logic ---
        /*
        File::delete(public_path('uploads/blogs/' . $blog->image));
        */

        // --- NEW Cloudinary Delete (optional) ---
        // If you want to delete from Cloudinary also:
        // $publicId = pathinfo($blog->image, PATHINFO_FILENAME);
        // Cloudinary::destroy('blogs/' . $publicId);

        $blog->delete();

        return response()->json([
            'status' => true,
            'message' => 'Blog deleted successfully'
        ]);
    }
}
