<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/login', [AuthController::class, 'login']);


// Route::post('register', [AuthController::class, 'register']);
// Route::post('login', [AuthController::class, 'login']);
// Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
// Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
// Route::post('me', [AuthController::class, 'me'])->middleware('auth:api');

Route::apiResource('posts', PostController::class);
