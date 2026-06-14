<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

Route::get('/doctors', [ApiController::class, 'getDoctors']);
Route::get('/doctors/{id}/slots', [ApiController::class, 'getDoctorSlots']);
Route::post('/appointments', [ApiController::class, 'bookAppointment']);
