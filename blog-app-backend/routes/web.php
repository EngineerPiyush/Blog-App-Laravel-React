<?php

use Illuminate\Support\Facades\Route;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-cloudinary', function () {
    try {
        $result = Cloudinary::upload('https://res.cloudinary.com/demo/image/upload/sample.jpg');
        return ['status' => true, 'url' => $result->getSecurePath()];
    } catch (\Exception $e) {
        return ['status' => false, 'error' => $e->getMessage()];
    }
});

