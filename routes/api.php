<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\AuthController;

Route::get('/doctors', [ApiController::class, 'getDoctors']);
Route::get('/doctors/{id}/slots', [ApiController::class, 'getDoctorSlots']);
Route::post('/appointments', [ApiController::class, 'bookAppointment']);
Route::get('/homepage', [ApiController::class, 'getHomepageData']);
Route::get('/about', [ApiController::class, 'getAboutData']);
Route::get('/contact', [ApiController::class, 'getContactData']);

Route::middleware('web')->prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['web', 'role:admin'])->post('/admin/homepage', [ApiController::class, 'updateHomepageData']);
Route::middleware(['web', 'role:admin'])->post('/admin/about', [ApiController::class, 'updateAboutData']);
Route::middleware(['web', 'role:admin'])->post('/admin/contact', [ApiController::class, 'updateContactData']);

