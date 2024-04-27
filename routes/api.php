<?php


use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\auth\ForgotPasswordController;
use App\Http\Controllers\auth\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



// Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
// Route::post('me', [AuthController::class, 'me'])->middleware('auth:api');


// Route::get('posts', [PostController::class, 'index']);



// public routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::post('password/forgot', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('password/reset', [ResetPasswordController::class, 'resetPassword']);





// protected routes

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [AuthController::class, 'logout']);

    // Route for posts

    Route::apiResource('posts', PostController::class);
    Route::put('/posts/image/{post}', [PostController::class, 'updatePostImage']);



    // Route for user profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/image', [ProfileController::class, 'updateProfileImage']);

    Route::get('/profile/{user}', [UserController::class, 'show']);


    // Route for sending connection request
    Route::post('/connections/send-request', [ConnectionController::class, 'sendRequest']);
    Route::post('/connections/accept/{connecftion}', [ConnectionController::class, 'accept']);
    Route::delete('/connections/reject/{connection}', [ConnectionController::class, 'reject']);
    Route::get('/connections', [ConnectionController::class, 'index']);
    Route::get('/connections/pending', [ConnectionController::class, 'pendingRequests']);
    
});