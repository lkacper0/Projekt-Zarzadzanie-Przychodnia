<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\AuthController;

Route::get('/doctors', [ApiController::class, 'getDoctors']);
Route::get('/doctors/{id}/slots', [ApiController::class, 'getDoctorSlots']);
Route::post('/appointments', [ApiController::class, 'bookAppointment']);

// Session-based API authentication routes
Route::middleware('web')->prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
