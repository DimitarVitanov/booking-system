<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EventController;

Route::get('/', fn() => redirect()->route('events.index'));

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events', [EventController::class, 'store'])->name('events.store');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
Route::post('/events/{event}/bookings', [EventController::class, 'storeBooking'])->name('events.bookings.store');
Route::patch('/bookings/{booking}/status', [EventController::class, 'updateBookingStatus'])->name('bookings.updateStatus');
