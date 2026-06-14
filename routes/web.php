<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('glowna');
});

Route::get('/o-nas', function () {
    return view('about');
});

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/create', [AdminController::class, 'create']);
Route::post('/admin', [AdminController::class, 'store']);

Route::get('/admin/reviews', [AdminController::class, 'reviews']);
Route::delete('/admin/reviews/{id}', [AdminController::class, 'destroyReview']);

Route::get('/admin/doctor-applications', [AdminController::class, 'doctorApplications']);
Route::post('/admin/doctor-applications/{id}/approve', [AdminController::class, 'approveDoctor']);

Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
Route::post('/admin/{id}/toggle-ban', [AdminController::class, 'toggleBan']);
