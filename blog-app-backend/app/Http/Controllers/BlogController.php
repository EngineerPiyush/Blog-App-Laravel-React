<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    // this method return all blogs
    public function index(Request $request)
    {
        $blogs = Blog::orderBy('created_at', 'DESC');

        if(!empty($request->keyword)){
            $blogs = $blogs->where('title','like','%'.$request->keyword.'%');
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
    //  this methos insert a  blog
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

        // save image here 
        $tempImage = TempImage::find($request->image_id);
        if ($tempImage != null) {
            // create new image name
            $imageExtArray = explode('.', $tempImage->name);
            $ext = last($imageExtArray);
            $imageName = time() . '-' . $blog->id . '.' . $ext;
            // update db record
            $blog->image = $imageName;
            $blog->save();
            // move file
            $sourcePath = public_path('uploads/temp/' . $tempImage->name);
            $destPath = public_path('uploads/blogs/' . $imageName);

            if (File::copy($sourcePath, $destPath)) {
                File::delete($sourcePath); // delete temp file
                $tempImage->delete();      // optional: remove DB record of temp image
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'blog created successfully',
            'data' => $blog
        ]);
    }
    // this method update a blog
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

        $tempImage = TempImage::find($request->image_id);
        if ($tempImage != null) {
            // delete old image if exists
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
                File::delete($sourcePath); // delete temp file
                $tempImage->delete();      // optional: remove DB record of temp image
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'blog updated successfully',
            'data' => $blog
        ]);
    }
    // this method will delete a blog
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if ($blog == null) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Blog not found',
                ]
            );
        }
        // Delete Blog Image First
        File::delete(public_path('uploads/blogs/' . $blog->image));

        // Delete Blog from DB
        $blog->delete();

        return response()->json([
                'status' => true,
                'message' => 'Blog deleted  successfully'
            ]
        );
    }
}
