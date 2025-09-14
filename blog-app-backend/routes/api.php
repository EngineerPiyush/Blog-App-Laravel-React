<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\TempImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/visit',function(Request $request){
    return response()->json([
        'message'=>'this api is working fine on /visit route'
    ]);
});

Route::post('blogs',[BlogController::class,'store']);
Route::post('save-image-temp',[TempImageController::class,'store']);
Route::get('blogs',[BlogController::class,'index']);
Route::get('/blog/{id}',[BlogController::class , 'show']);
Route::put('/blog/{id}',[BlogController::class , 'update']);
Route::delete('/blog/{id}',[BlogController::class , 'destroy']);

?>