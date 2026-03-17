<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


//Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    // Events
    Route::apiResource('events', EventController::class)
        ->only(['index', 'store', 'show', 'destroy'])
        ->names('api.events');

    // Bookings (nested under events)
    Route::prefix('events/{event}/bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/', [BookingController::class, 'store']);
    });

    // Update booking status (standalone)
    Route::patch('bookings/{booking}', [BookingController::class, 'updateStatus']);
});