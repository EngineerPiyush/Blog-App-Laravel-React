<?php

use Illuminate\Support\Facades\Route;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-cloudinary', function () {
    try {
        $upload = Cloudinary::upload('https://res.cloudinary.com/demo/image/upload/sample.jpg', ['folder' => 'test_uploads']);
        // Try to extract a URL:
        $url = is_object($upload) && method_exists($upload, 'getSecurePath') ? $upload->getSecurePath() : ($upload['secure_url'] ?? null);
        return response()->json(['status' => true, 'url' => $url, 'raw' => $upload]);
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
    }
});
