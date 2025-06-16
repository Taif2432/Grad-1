<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthAPIController;
use App\Http\Controllers\API\AdminAPIController;
use App\Http\Controllers\API\ClientSessionAPIController;
use App\Http\Controllers\API\ProfessionalSessionAPIController;
use App\Http\Controllers\API\ProfessionalContentAPIController;
use App\Http\Controllers\API\AdminContentAPIController;
use App\Http\Controllers\API\UserContentAPIController;
use App\Http\Controllers\API\ContentTypeController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthAPIController::class, 'register']);
Route::post('/login', [AuthAPIController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('/logout', [AuthAPIController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'approved'])->group(function () {
    // Protected routes go here
    Route::get('/account', [AuthAPIController::class, 'viewAccount']);
    Route::put('/update-account', [AuthAPIController::class, 'updateAccount']);
    Route::put('/update-password', [AuthAPIController::class, 'updatePassword']);
    Route::delete('/delete-account', [AuthAPIController::class, 'deleteAccount']);
    });

    //client routes
    Route::middleware(['auth:sanctum', 'approved'])->group(function () {
    Route::prefix('client')->group(function () {
        Route::post('/book-session', [ClientSessionAPIController::class, 'bookSession']);
        Route::get('/my-sessions', [ClientSessionAPIController::class, 'mySessions']);
        Route::get('/join-session/{id}', [ClientSessionAPIController::class, 'joinSession']);
        Route::patch('/cancel-my-session/{id}', [ClientSessionAPIController::class, 'cancelMySession']);
        Route::delete('/delete-session/{id}', [ClientSessionAPIController::class, 'deleteSession']);
        //view pro availability
        Route::get('/availabilities', [ClientSessionAPIController::class, 'viewAvailableSessions']);
        Route::post('/feedback', [ClientSessionAPIController::class,'feedback']);

    }); });

    //Professional routes
    Route::middleware(['auth:sanctum', 'approved'])->group(function () {
    Route::prefix('professional')->group(function () {
        //session
        Route::get('/upcoming-sessions', [ProfessionalSessionAPIController::class, 'upcomingSessions']);
        Route::get('/past-sessions', [ProfessionalSessionAPIController::class, 'pastSessions']);
        Route::get('/join-session/{id}', [ProfessionalSessionAPIController::class, 'joinSession']);
        Route::patch('/complete-session/{id}', [ProfessionalSessionAPIController::class, 'completeSession']);
        Route::patch('/cancel-session/{id}', [ProfessionalSessionAPIController::class, 'cancelSession']);
        //content
        Route::get('/view_content', [ProfessionalContentAPIController::class, 'index']);
        Route::post('/add-content', [ProfessionalContentAPIController::class, 'store']);
        Route::put('/update-content/{id}', [ProfessionalContentAPIController::class, 'update']);
        Route::delete('/delete-content/{id}', [ProfessionalContentAPIController::class, 'destroy']);
        //availability
        Route::post('/availability', [ProfessionalContentAPIController::class, 'storeAvailability']);
    }); });

    // Admin Routes
    Route::middleware(['auth:sanctum', 'is.admin'])->group(function () {
    Route::prefix('admin')->group(function () {
         //users info
        Route::get('/pending-users', [AdminAPIController::class, 'pendingUsers']);
        Route::post('/approve-user/{id}', [AdminAPIController::class, 'approveUser']);
        Route::delete('/reject-user/{id}', [AdminAPIController::class, 'rejectUser']);
         //view session loggs
        Route::get('/session-logs', [AdminAPIController::class,'viewLogs']);
         //content 
        Route::get('/view-content', [AdminContentAPIController::class, 'index']);
        Route::delete('/delete-content/{id}', [AdminContentAPIController::class, 'destroy']);
}); });

// User Routes
Route::middleware('auth:sanctum')->get('/content', [UserContentAPIController::class, 'index']);
//
Route::get('/user/content', [UserContentAPIController::class, 'show']);
Route::get('/user/content-types', [UserContentAPIController::class, 'getAvailableTypes']);
Route::get('/content-types', [ContentTypeController::class, 'index']);
